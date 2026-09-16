#!/usr/bin/env python3
"""Convert the MediaWiki XML export into Hugo Markdown pages.

Run from anywhere:  python3 migration/convert.py

This is a one-time import. Re-running it overwrites the generated pages,
so make hand edits after the import has been committed.
"""
import json
import re
import shutil
import subprocess
import sys
import xml.etree.ElementTree as ET
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
EXPORT = ROOT / "migration/export/hlwiki-full-history.xml"
FILES_JSON = ROOT / "migration/export/files.json"
CONTENT = ROOT / "content"
STATIC = ROOT / "static"
DOWNLOADS = ROOT / "files"
NS = {"m": "http://www.mediawiki.org/xml/export-0.11/"}

# HTML tags MediaWiki lets through. Anything else (e.g. <username>) is shown
# as literal text on the old wiki, so it gets escaped before pandoc sees it.
ALLOWED_TAGS = {
    "abbr", "b", "bdi", "bdo", "big", "blockquote", "br", "caption", "center",
    "cite", "code", "data", "dd", "del", "dfn", "div", "dl", "dt", "em", "font",
    "gallery", "h1", "h2", "h3", "h4", "h5", "h6", "hr", "html", "i", "ins",
    "kbd", "li", "mark", "math", "nowiki", "ol", "p", "pre", "q", "ref",
    "references", "rp", "rt", "ruby", "s", "samp", "small", "source", "span",
    "strike", "strong", "sub", "sup", "syntaxhighlight", "table", "td", "th",
    "time", "tr", "tt", "u", "ul", "var", "wbr",
}
LITERAL = re.compile(
    r"(<nowiki>.*?</nowiki>|<pre\b.*?</pre>|<syntaxhighlight\b.*?</syntaxhighlight>)",
    re.S | re.I,
)
SPECIAL_PAGES = {
    "recentchanges": "/recent-changes/",
    "allpages": "/all-pages/",
    "categories": "/categories/",
    "search": "/search/",
}
INFOBOX_FIELDS = [
    "name", "image", "creator", "latest_version", "date_first_release",
    "date_latest_release", "os", "type", "license", "website",
]


def norm_title(t):
    t = re.sub(r"\s+", " ", t.replace("_", " ")).strip()
    return t[:1].upper() + t[1:]


def norm_file(name):
    name = re.sub(r"^:?(file|image|media):", "", name.strip(), flags=re.I)
    name = name.strip().replace(" ", "_")
    return name[:1].upper() + name[1:]


def simple_slug(t):
    return re.sub(r"[^a-z0-9]+", "-", t.lower().replace("'", "")).strip("-")


def page_slug(t):
    """'HTTPTunneling' -> 'http-tunneling', "Virtual1's Guide" -> 'virtual1s-guide'."""
    t = re.sub(r"([A-Z]+)([A-Z][a-z])", r"\1-\2", t)
    t = re.sub(r"([a-z0-9])([A-Z])", r"\1-\2", t)
    return simple_slug(t)


def loose_key(t):
    return re.sub(r"[\s_]+", "", t).lower()


def anchorize(a):
    """Match Hugo's default heading IDs."""
    a = a.replace("_", " ").strip().lower()
    a = re.sub(r"[^\w\- ]", "", a)
    return a.replace(" ", "-")


def plain_text(inlines):
    out = []
    for i in inlines:
        if i["t"] == "Str":
            out.append(i["c"])
        elif i["t"] in ("Space", "SoftBreak"):
            out.append(" ")
    return "".join(out)


class Splice:
    def __init__(self, items):
        self.items = items


class Site:
    """Everything that is known about the wiki as a whole."""

    def __init__(self, root):
        self.pages = {}  # normalized title -> dict(ns, title, text, revisions)
        for p in root.findall("m:page", NS):
            revs = p.findall("m:revision", NS)
            contributors = []
            for r in revs:
                name = r.findtext("m:contributor/m:username", None, NS)
                if name and name not in contributors:
                    contributors.append(name)
            title = p.findtext("m:title", "", NS)
            self.pages[norm_title(title)] = {
                "ns": int(p.findtext("m:ns", "0", NS)),
                "title": title,
                "text": revs[-1].findtext("m:text", "", NS) or "",
                "revisions": len(revs),
                "last_edited": revs[-1].findtext("m:timestamp", "", NS)[:10],
                "contributors": contributors,
            }
        self.files = {
            f["name"]: f for f in json.load(open(FILES_JSON))["query"]["allimages"]
        }
        self.urls = {}
        for key, page in self.pages.items():
            if page["ns"] == 0:
                self.urls[key] = "/" if key == "Main Page" else f"/{page_slug(page['title'])}/"
            elif page["ns"] == 2 and "/" not in page["title"]:
                self.urls[key] = f"/users/{simple_slug(page['title'][5:])}/"
            elif page["ns"] == 4:
                self.urls[key] = "/about/"
        self.loose = {loose_key(k): v for k, v in self.urls.items()}

    def file_url(self, name):
        name = norm_file(name)
        f = self.files.get(name)
        if not f:
            return None
        return ("/images/" if f["mime"].startswith("image/") else "/files/") + name

    def resolve(self, target):
        t = target.strip().lstrip(":")
        anchor = ""
        if "#" in t:
            t, anchor = t.split("#", 1)
            anchor = "#" + anchorize(anchor)
        if not t:
            return anchor or None
        prefix, _, rest = t.partition(":")
        prefix = prefix.strip().lower()
        if rest and prefix in ("file", "image", "media"):
            return self.file_url(rest)
        if rest and prefix == "category":
            return f"/categories/{simple_slug(norm_title(rest))}/"
        if rest and prefix == "special":
            return SPECIAL_PAGES.get(rest.strip().lower().replace(" ", ""))
        if rest and prefix in ("template", "mediawiki", "help"):
            return None
        url = self.urls.get(norm_title(t)) or self.loose.get(loose_key(t))
        return url + anchor if url else None


class Converter:
    def __init__(self, site, title):
        self.site = site
        self.title = title
        self.tokens = {}
        self.front = {}
        self.red_links = set()
        self.missing_images = set()
        self.unknown_templates = set()

    # --- wikitext preprocessing -------------------------------------------
    def token(self, replacement, block=False):
        key = f"HLWTOK{len(self.tokens):04d}Z"
        self.tokens[key] = replacement
        return f"\n\n{key}\n\n" if block else key

    def templates(self, text):
        out, i = [], 0
        while True:
            s = text.find("{{", i)
            if s < 0:
                break
            depth, j = 0, s
            while j < len(text):
                if text.startswith("{{", j):
                    depth += 1
                    j += 2
                elif text.startswith("}}", j):
                    depth -= 1
                    j += 2
                    if depth == 0:
                        break
                else:
                    j += 1
            if depth:
                break
            out.append(text[i:s])
            out.append(self.template(text[s + 2 : j - 2]))
            i = j
        out.append(text[i:])
        return "".join(out)

    @staticmethod
    def split_params(inner):
        parts, buf, depth, i = [], [], 0, 0
        while i < len(inner):
            two = inner[i : i + 2]
            if two in ("[[", "{{"):
                depth += 1
                buf.append(two)
                i += 2
            elif two in ("]]", "}}"):
                depth -= 1
                buf.append(two)
                i += 2
            elif inner[i] == "|" and depth == 0:
                parts.append("".join(buf))
                buf = []
                i += 1
            else:
                buf.append(inner[i])
                i += 1
        parts.append("".join(buf))
        return parts

    def template(self, inner):
        parts = self.split_params(inner)
        name = parts[0].strip().replace("_", " ").lower()
        args = [p.strip() for p in parts[1:]]
        if name == "hotlinenav":
            self.front["nav"] = "protocol"
            return ""
        if name in ("iconbox", "icon") and args:
            return self.token(f'{{{{< iconbox "{args[0]}" >}}}}')
        if name == "communityicon" and args:
            return self.token(f'{{{{< communityicon "{args[0]}" >}}}}')
        if name == "bannerbox" and args:
            return self.token(f'{{{{< banner "{args[0]}" >}}}}', block=True)
        if name == "infobox software":
            box = {}
            for a in args:
                k, _, v = a.partition("=")
                k, v = k.strip(), v.strip()
                if k not in INFOBOX_FIELDS or not v:
                    continue
                if k == "image":
                    m = re.match(r"\[\[(?:File|Image):([^|\]]+)", v, re.I)
                    url = m and self.site.file_url(m.group(1))
                    if url:
                        box[k] = url
                    else:
                        self.missing_images.add(v)
                    continue
                box[k] = Converter(self.site, self.title).inline(v)
            self.front["infobox"] = {k: box[k] for k in INFOBOX_FIELDS if k in box}
            return ""
        self.unknown_templates.add(parts[0].strip())
        return "{{" + inner + "}}"

    def gallery(self, m):
        items = []
        for line in m.group(1).strip().splitlines():
            f, _, caption = line.partition("|")
            url = self.site.file_url(f)
            if url:
                items.append(f"{url}|{caption.strip()}")
            elif f.strip():
                self.missing_images.add(f.strip())
        if not items:
            return ""
        body = "\n".join(items)
        return self.token(f"{{{{< gallery >}}}}\n{body}\n{{{{< /gallery >}}}}", block=True)

    def categories(self, m):
        self.front.setdefault("categories", []).append(norm_title(m.group(1)))
        return ""

    @staticmethod
    def escape_tags(chunk):
        def fix(m):
            if m.group(2).lower() in ALLOWED_TAGS:
                return m.group(0)
            return "&lt;" + m.group(0)[1:-1] + "&gt;"

        return re.sub(r"<(/?)([a-zA-Z][a-zA-Z0-9]*)([^<>]*)>", fix, chunk)

    def preprocess(self, text):
        chunks = LITERAL.split(text)
        for n in range(0, len(chunks), 2):  # even chunks are outside <nowiki>/<pre>
            c = chunks[n]
            c = re.sub(r"<gallery[^>]*>(.*?)</gallery>", self.gallery, c, flags=re.S | re.I)
            c = self.templates(c)
            c = re.sub(r"\[\[(https?://[^\s\]|]+)\s+([^\]]*)\]\]", r"[\1 \2]", c)
            c = re.sub(r"\[\[\s*Category\s*:\s*([^\]|]+)(?:\|[^\]]*)?\]\]\n?", self.categories, c, flags=re.I)
            if "__NOTOC__" in c:
                self.front["toc"] = False
            if "__TOC__" in c:
                self.front["toc"] = True
            c = re.sub(r"__[A-Z]+__", "", c)
            chunks[n] = self.escape_tags(c)
        return "".join(chunks)

    # --- pandoc AST rewriting ---------------------------------------------
    def walk(self, x):
        if isinstance(x, list):
            out = []
            for item in x:
                r = self.walk(item)
                out.extend(r.items) if isinstance(r, Splice) else out.append(r)
            return out
        if not isinstance(x, dict) or "t" not in x:
            return x
        if x["t"] == "Link":
            attr, inlines, (target, title) = x["c"]
            inlines = self.walk(inlines)
            if "wikilink" not in attr[1]:
                return {"t": "Link", "c": [attr, inlines, [target, title]]}
            url = self.site.resolve(target)
            if url is None:
                self.red_links.add(target)
                return Splice(inlines)
            if url.startswith(("/files/", "/images/")) and plain_text(inlines) == target:
                inlines = [{"t": "Str", "c": url.rsplit("/", 1)[1]}]
            return {"t": "Link", "c": [["", [], []], inlines, [url, ""]]}
        if x["t"] == "Image":
            attr, inlines, (src, _) = x["c"]
            url = self.site.file_url(src)
            if url is None:
                self.missing_images.add(src)
                return Splice([])
            alt = plain_text(inlines)
            if norm_file(alt) == norm_file(src):
                alt = Path(norm_file(src)).stem.replace("_", " ")
            width = [[k, v] for k, v in attr[2] if k == "width"]
            return {"t": "Image", "c": [["", [], width], [{"t": "Str", "c": alt}], [url, ""]]}
        if "c" in x:
            x = dict(x, c=self.walk(x["c"]))
        return x

    def pandoc(self, wikitext):
        doc = json.loads(run(["pandoc", "-f", "mediawiki", "-t", "json"], wikitext))
        doc["blocks"] = self.walk(doc["blocks"])
        md = run(["pandoc", "-f", "json", "-t", "gfm", "--wrap=none"], json.dumps(doc))
        for key, value in self.tokens.items():
            md = md.replace(key, value)
        # Complex tables come out as raw HTML; match the Markdown table style.
        md = md.replace("<table>", '<div class="table-wrap">\n<table class="wikitable" border="1" cellpadding="4" cellspacing="0">')
        md = md.replace("</table>", "</table>\n</div>")
        return md.strip() + "\n"

    def convert(self, text):
        return self.pandoc(self.preprocess(text))

    def inline(self, text):
        return self.convert(text).strip()


def run(cmd, stdin):
    r = subprocess.run(cmd, input=stdin, capture_output=True, text=True)
    if r.returncode:
        sys.exit(f"{' '.join(cmd)} failed:\n{r.stderr}")
    return r.stdout


def yaml_scalar(v):
    if isinstance(v, bool):
        return "true" if v else "false"
    if isinstance(v, int):
        return str(v)
    return json.dumps(v, ensure_ascii=False)  # JSON strings/lists are valid YAML


def write_page(path, front, body):
    lines = ["---"]
    for k, v in front.items():
        if isinstance(v, dict):
            lines.append(f"{k}:")
            lines += [f"  {k2}: {yaml_scalar(v2)}" for k2, v2 in v.items()]
        else:
            lines.append(f"{k}: {yaml_scalar(v)}")
    lines.append("---")
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text("\n".join(lines) + "\n\n" + body)


def main():
    site = Site(ET.parse(EXPORT).getroot())
    if CONTENT.exists():
        shutil.rmtree(CONTENT)
    report = {"red": {}, "images": {}, "templates": {}}
    written = {}

    def record(conv):
        for kind, found in (("red", conv.red_links), ("images", conv.missing_images), ("templates", conv.unknown_templates)):
            for item in found:
                report[kind].setdefault(item, []).append(conv.title)

    for key, page in sorted(site.pages.items()):
        ns, title = page["ns"], page["title"]
        if ns == 0:
            path = CONTENT / "_index.md" if key == "Main Page" else CONTENT / f"{page_slug(title)}.md"
        elif ns == 2 and key in site.urls:
            path = CONTENT / "users" / f"{simple_slug(title[5:])}.md"
            title = title[5:]
        elif ns == 4:
            path = CONTENT / "about.md"
            title = "About"
        elif ns == 14:
            path = CONTENT / "categories" / simple_slug(title[9:]) / "_index.md"
            title = title[9:]
        else:
            continue  # File:, Template:, MediaWiki: pages are handled separately
        if path in written:
            sys.exit(f"Slug collision: {page['title']} and {written[path]} -> {path}")
        written[path] = page["title"]
        conv = Converter(site, page["title"])
        body = conv.convert(page["text"])
        front = {"title": title, **conv.front}
        front["mediawiki"] = {
            "title": page["title"],
            "revisions": page["revisions"],
            "last_edited": page["last_edited"],
            "contributors": page["contributors"],
        }
        write_page(path, front, body)
        record(conv)

    # Template:HotlineNav becomes an editable navbox page.
    nav = site.pages["Template:HotlineNav"]["text"]
    nav = re.sub(r"</?div[^>]*>|<center>.*?</center>|<hr\s*/?>", "", nav, flags=re.S)
    conv = Converter(site, "Template:HotlineNav")
    write_page(
        CONTENT / "navboxes/protocol/index.md",
        {"title": "Hotline Protocol", "build": {"render": "never", "list": "never"}},
        conv.convert(nav),
    )
    record(conv)

    # MediaWiki:Sidebar becomes an editable sidebar page.
    sections = {"navigation": "Navigation", "discord": "Discord", "partners": "Partners"}
    md, current = [], None
    for line in site.pages["MediaWiki:Sidebar"]["text"].splitlines():
        if line.startswith("**") and current:
            target, _, label = line[2:].strip().partition("|")
            if target == "mainpage":
                url = "/"
            elif target.startswith("http"):
                url = target
            else:
                url = site.resolve(target)
            if url:
                md.append(f"- [{label}]({url})")
            if target == "Special:RecentChanges" and current == "Navigation":
                md += ["- [All pages](/all-pages/)", "- [Categories](/categories/)"]
        elif line.startswith("*"):
            current = sections.get(line[1:].strip().lower())
            if current:
                md.append(f"\n### {current}\n")
    write_page(
        CONTENT / "sidebar/index.md",
        {"title": "Sidebar", "build": {"render": "never", "list": "never"}},
        "\n".join(md).strip() + "\n",
    )

    for name, layout in (("recent-changes", "Recent changes"), ("all-pages", "All pages"), ("search", "Search")):
        write_page(CONTENT / f"{name}.md", {"title": layout, "layout": name}, "")

    # Uploaded images live in git; large downloads stay out of it.
    (STATIC / "images").mkdir(parents=True, exist_ok=True)
    downloads = {}
    for name, f in site.files.items():
        if f["mime"].startswith("image/"):
            src = DOWNLOADS / name
            if src.exists():
                shutil.move(src, STATIC / "images" / name)
            if name.endswith(".svg") and not (STATIC / "images" / f"{name}.png").exists():
                subprocess.run(["rsvg-convert", "-w", "256", "--keep-aspect-ratio", str(STATIC / "images" / name), "-o", str(STATIC / "images" / f"{name}.png")], check=True)
        else:
            downloads[name] = {"size": f["size"], "mime": f["mime"]}
    (ROOT / "data").mkdir(exist_ok=True)
    (ROOT / "data/files.json").write_text(json.dumps(downloads, indent=2, sort_keys=True) + "\n")

    write_htaccess(site)

    print(f"Wrote {len(written)} pages.")
    for kind, label in (("templates", "Unknown templates"), ("images", "Missing images"), ("red", "Links to pages that never existed")):
        if report[kind]:
            print(f"\n{label}:")
            for item, pages in sorted(report[kind].items()):
                print(f"  {item}  (on: {', '.join(sorted(set(pages)))})")


def write_htaccess(site):
    def pattern(title):
        return re.escape(title.replace(" ", "_"))

    lines = [
        "# Generated by migration/convert.py. Keeps old MediaWiki links working.",
        "# Nothing here forces HTTPS: old computers need plain HTTP.",
        "AddDefaultCharset UTF-8",
        "AddType application/x-stuffit .sit",
        "AddType application/mac-binhex40 .hqx",
        "AddType application/x-7z-compressed .7z",
        "AddType application/vnd.rar .rar",
        "ErrorDocument 404 /404.html",
        "",
        "RewriteEngine On",
        "RewriteRule ^index\\.php/?$ / [R=301,L]",
    ]
    targets = {k: v for k, v in site.urls.items()}
    for key, page in site.pages.items():
        if page["ns"] == 14:
            targets[key] = site.resolve(page["title"])
    targets.update({"Special:RecentChanges": "/recent-changes/", "Special:AllPages": "/all-pages/"})
    for key, url in sorted(targets.items()):
        title = site.pages[key]["title"] if key in site.pages else key
        p = pattern(title)
        lines.append(f"RewriteRule ^index\\.php/{p}$ {url} [R=301,L,NE]")
        lines.append(f"RewriteCond %{{QUERY_STRING}} (^|&)title={p}(&|$)")
        lines.append(f"RewriteRule ^index\\.php$ {url}? [R=301,L,NE]")
    for name in sorted(site.files):
        url = site.file_url(name)
        p = re.escape(name)
        lines.append(f"RewriteRule ^images/[0-9a-f]/[0-9a-f]{{2}}/{p}$ {url} [R=301,L,NE]")
        lines.append(f"RewriteRule ^index\\.php/File:{p}$ {url} [R=301,L,NE]")
    (STATIC / ".htaccess").write_text("\n".join(lines) + "\n")


if __name__ == "__main__":
    main()

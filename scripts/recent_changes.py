#!/usr/bin/env python3
"""Write data/recentchanges.json from git history, for the Recent changes page.

Run before `hugo` (the deploy workflow does this automatically).
"""
import json
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
LIMIT = 100


def page_ref(path):
    """content/foo.md -> /foo, content/categories/x/_index.md -> /categories/x"""
    ref = "/" + path.removeprefix("content/").removesuffix(".md")
    ref = ref.removesuffix("/_index").removesuffix("/index")
    return ref if ref != "/_index" else "/"


def main():
    log = subprocess.run(
        ["git", "log", f"--max-count={LIMIT}", "--date=iso-strict", "--name-status",
         "--pretty=format:%x1e%H%x1f%ae%x1f%ad%x1f%s", "--", "content"],
        cwd=ROOT, capture_output=True, text=True, check=True,
    ).stdout
    changes = []
    for entry in filter(None, log.split("\x1e")):
        header, *lines = entry.strip("\n").split("\n")
        commit, email, date, subject = header.split("\x1f")
        # Show a username, never a real name: the email's local part, after any "12345+".
        author = email.split("@")[0].split("+")[-1]
        pages = []
        for line in lines:
            status, *paths = line.split("\t")
            path = paths[-1]
            if path.endswith(".md") and not path.startswith(("content/sidebar/", "content/navboxes/")):
                pages.append({"ref": page_ref(path), "status": status[0]})
        changes.append({"hash": commit, "author": author, "date": date, "subject": subject, "pages": pages})
    out = ROOT / "data/recentchanges.json"
    out.parent.mkdir(exist_ok=True)
    out.write_text(json.dumps(changes, indent=1) + "\n")
    print(f"Wrote {len(changes)} changes to {out.relative_to(ROOT)}")


if __name__ == "__main__":
    main()

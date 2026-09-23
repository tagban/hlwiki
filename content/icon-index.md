---
title: "Icon Index"
nav: "protocol"
categories: ["Hotline Icons", "Development"]
toc: true
---

Every icon in the [community icon archive](/community-icons/) has a row in one CSV file
listing the words written on it, what it shows, and its main colors. With it you can
search the archive for "flag", "skull", "starcraft" or "red" instead of scrolling through
6,500 numbers. The [icon gallery](https://hlwiki.com/ik0ns/) searches with it, and so does
[Invigoration](https://github.com/tagban/invigoration)'s icon picker. You're welcome to use
it in your own client, server, tracker or tool.

## Where to get it

| File | What it is |
|---|---|
| [`https://hlwiki.com/ik0ns/ik0ns.csv`](https://hlwiki.com/ik0ns/ik0ns.csv) | The index: one row per icon (about 400 KB). |
| `https://hlwiki.com/ik0ns/<id>.png` | One icon, by number. These URLs never change. |
| [`https://hlwiki.com/ik0ns/ik0ns.zip`](https://hlwiki.com/ik0ns/ik0ns.zip) | Every icon in one download (about 40 MB), as `ik0ns/<id>.png`. |

The CSV and the zip are rebuilt from the same archive on every site update, so they always
agree with each other and with the PNGs on the server. Please cache them rather than
fetching on every search. Downloading once per session, or once a day, is plenty.

## Format

UTF-8 (no byte-order mark), comma-separated, one header row, `\n` line endings, and standard
CSV quoting: a field that contains a comma or a double quote is wrapped in double quotes, and a
quote inside it is doubled (`""`). Use a real CSV parser, not a split on commas.

```
id,text,tags,colors
127,Bad Moon Icons 5.0,bad moon icons;icon set;hammer and sickle;soviet;communist;dot matrix,gray;black;brown
2615,Mountain Dew,mountain dew;soda;drink;logo;brand;green,green;white;red
7633,スレイヤーズ,slayers;anime;japanese;lina inverse;katakana,gray;white;orange
9421,,bomb;red;box;small icon,white;gray
```

| Column | Contents |
|---|---|
| `id` | The icon number, as a Hotline client sends it. Matches the PNG's file name. Almost all are 0–65535; the archive also has one `-1`. |
| `text` | The lettering on the icon, as written (any language). Empty when there isn't any, or it can't be read. |
| `tags` | Lowercase search terms separated by `;`: what's pictured (flag, eye, skull, hand, dragon...), a recognizable franchise, brand, band, team or game, and the style (anime, photo, pixel art...). A tag can be several words (`hammer and sickle`). |
| `colors` | Up to four main colors, most dominant first, separated by `;`, from this fixed set: `black`, `white`, `gray`, `red`, `pink`, `orange`, `brown`, `gold`, `yellow`, `green`, `cyan`, `blue`, `purple`. |

Rows are sorted by `id`. Columns may be added at the end one day, so read columns by their
header name, and ignore any you don't know.

### NSFW icons

Icons with nudity or sexual content carry the tag `nsfw`. The gallery and Invigoration both
hide them unless the user ticks **Show NSFW**. Please do the same: hide them by default.

### Removed icons

A few icons have been taken out of the archive on purpose (Nazi imagery). They aren't in the
CSV, the zip, or on the server any more, and
[`scripts/removed-ik0ns.txt`](https://github.com/tagban/hlwiki/blob/main/scripts/removed-ik0ns.txt)
lists their numbers. If you keep your own copy of the icons, delete anything the current zip
doesn't have.

## Searching the way the gallery does

To make results feel the same everywhere, the gallery and Invigoration follow these rules:

1. If the search is only digits, it's an icon number: `41` shows 41, 410–419, 4100...
2. Otherwise, take the icon's words from all three columns (`text`, then `tags`, then
   `colors`). Split them on spaces, lowercase them, and trim punctuation off the ends.
3. Split the search the same way. An icon matches when **every** search word is the start of
   **one** of its words. So `eye` finds "eyes", `red flag` finds red flags, and `red` doesn't
   find "tired".

## Examples

### Python

```python
import csv, io, urllib.request

def load_icon_index(url="https://hlwiki.com/ik0ns/ik0ns.csv"):
    with urllib.request.urlopen(url) as response:
        rows = csv.DictReader(io.TextIOWrapper(response, encoding="utf-8"))
        return {
            int(row["id"]): {
                "text": row["text"],
                "tags": [t for t in row["tags"].split(";") if t],
                "colors": [c for c in row["colors"].split(";") if c],
            }
            for row in rows
        }

PUNCTUATION = "\"'.,!?:;()-*"

def words(text):
    return [w.strip(PUNCTUATION) for w in text.lower().split() if w.strip(PUNCTUATION)]

def search(index, query, show_nsfw=False):
    query = query.strip()
    wanted = words(query)
    for icon_id, icon in index.items():
        if "nsfw" in icon["tags"] and not show_nsfw:
            continue
        if query.isdigit():
            if str(icon_id).startswith(query):
                yield icon_id
            continue
        own = words(" ".join([icon["text"], *icon["tags"], *icon["colors"]]))
        if wanted and all(any(w.startswith(q) for w in own) for q in wanted):
            yield icon_id

index = load_icon_index()
for icon_id in search(index, "red flag"):
    print(icon_id, f"https://hlwiki.com/ik0ns/{icon_id}.png", index[icon_id]["tags"])
```

### JavaScript (browser or Node 18+)

```js
async function loadIconIndex(url = 'https://hlwiki.com/ik0ns/ik0ns.csv') {
  const text = await (await fetch(url)).text();
  const rows = parseCsv(text);
  const header = rows.shift();
  const col = (name) => header.indexOf(name);
  const list = (s) => (s || '').split(';').filter(Boolean);
  const index = new Map();
  for (const row of rows) {
    if (!row[col('id')]) continue;
    index.set(Number(row[col('id')]), {
      text: row[col('text')] || '',
      tags: list(row[col('tags')]),
      colors: list(row[col('colors')]),
    });
  }
  return index;
}

// Minimal RFC 4180 parser: quoted fields, doubled quotes, commas and newlines inside quotes.
function parseCsv(text) {
  const rows = []; let row = [], field = '', quoted = false;
  for (let i = 0; i < text.length; i++) {
    const c = text[i];
    if (quoted) {
      if (c === '"' && text[i + 1] === '"') { field += '"'; i++; }
      else if (c === '"') quoted = false;
      else field += c;
    } else if (c === '"') quoted = true;
    else if (c === ',') { row.push(field); field = ''; }
    else if (c === '\n' || c === '\r') {
      if (c === '\r' && text[i + 1] === '\n') i++;
      row.push(field); rows.push(row); row = []; field = '';
    } else field += c;
  }
  if (field || row.length) { row.push(field); rows.push(row); }
  return rows;
}

const words = (s) => s.toLowerCase().split(/\s+/)
  .map((w) => w.replace(/^["'.,!?:;()\-*]+|["'.,!?:;()\-*]+$/g, ''))
  .filter(Boolean);

function searchIcons(index, query, showNsfw = false) {
  query = query.trim();
  const wanted = words(query);
  const results = [];
  for (const [id, icon] of index) {
    if (icon.tags.includes('nsfw') && !showNsfw) continue;
    if (/^\d+$/.test(query)) {
      if (String(id).startsWith(query)) results.push(id);
      continue;
    }
    const own = words([icon.text, ...icon.tags, ...icon.colors].join(' '));
    if (wanted.length && wanted.every((q) => own.some((w) => w.startsWith(q)))) results.push(id);
  }
  return results;
}

const index = await loadIconIndex();
console.log(searchIcons(index, 'starcraft').map((id) => `https://hlwiki.com/ik0ns/${id}.png`));
```

### C# / .NET

.NET's own `TextFieldParser` (in `Microsoft.VisualBasic.FileIO`, part of every .NET install)
handles the quoting:

```csharp
using Microsoft.VisualBasic.FileIO;

using var http = new HttpClient();
using var parser = new TextFieldParser(await http.GetStreamAsync("https://hlwiki.com/ik0ns/ik0ns.csv"))
{
    TextFieldType = FieldType.Delimited,
    HasFieldsEnclosedInQuotes = true,
};
parser.SetDelimiters(",");
var header = parser.ReadFields()!;
int Col(string name) => Array.IndexOf(header, name);

var icons = new Dictionary<int, (string Text, string[] Tags, string[] Colors)>();
while (parser.ReadFields() is { } row)
{
    icons[int.Parse(row[Col("id")])] = (
        row[Col("text")],
        row[Col("tags")].Split(';', StringSplitOptions.RemoveEmptyEntries),
        row[Col("colors")].Split(';', StringSplitOptions.RemoveEmptyEntries));
}

Console.WriteLine($"{icons.Count} icons; 2615 is \"{icons[2615].Text}\"");
```

Invigoration's
[`HotlineIconIndex.cs`](https://github.com/tagban/invigoration/blob/main/dotnet/src/Invigoration.App/Models/HotlineIconIndex.cs)
is a complete implementation: it downloads and caches the file, parses it without any
library, and follows the search rules above.

### Command line

```sh
curl -s https://hlwiki.com/ik0ns/ik0ns.csv -o ik0ns.csv
grep -i 'dragon' ik0ns.csv | cut -d, -f1     # rough: fine for a quick look, not quoted fields
```

## Fixing or adding entries

The index was built by reading every icon: lettering by text recognition and by eye, subjects
by eye, and colors measured from the pixels. Some tags will be wrong, and very small icons
have only general ones. To fix one, edit
[`static/ik0ns/ik0ns.csv`](https://github.com/tagban/hlwiki/blob/main/static/ik0ns/ik0ns.csv)
on GitHub and open a pull request. When you add icons to `static/ik0ns/`, give each one a
row too. Keep tags lowercase and separated by `;`, and use only the color names listed above.

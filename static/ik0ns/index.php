<?php
// Every icon in this folder, by number, in natural order (1, 2, 10 instead of 1, 10, 2).
$ids = array();
foreach (glob('./*.png') as $file) {
    $ids[] = basename($file, '.png');
}
natsort($ids);
$ids = array_values($ids);

// Icons ik0ns.csv tags "nsfw" (id,text,tags,colors; tags separated by semicolons) stay out of
// results unless Show NSFW is ticked. Worked out here so it holds even before the CSV loads.
$nsfw = array();
if (($csv = @fopen('./ik0ns.csv', 'r')) !== false) {
    while (($row = fgetcsv($csv, 0, ',', '"', '')) !== false) {
        if (isset($row[2]) && in_array('nsfw', array_map('trim', explode(';', strtolower($row[2]))), true)) {
            $nsfw[] = trim($row[0]);
        }
    }
    fclose($csv);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Icon Gallery</title>
    <style>
        body { font-family: sans-serif; background: #1a1a1a; color: white; margin: 0; }
        .search { position: sticky; top: 0; background: #1a1a1a; padding: 15px 20px 5px; z-index: 1; }
        .search input[type=search] { width: 100%; max-width: 520px; font-size: 16px; padding: 8px 10px; border-radius: 6px; border: 1px solid #555; background: #262626; color: white; }
        .search p { font-size: 12px; color: #999; margin: 6px 0 0; }
        .search a { color: #8cf; }
        .nsfw-toggle { font-size: 14px; color: #ccc; margin-left: 12px; white-space: nowrap; }
        button { font-size: 14px; padding: 6px 12px; border-radius: 6px; border: 1px solid #555; background: #333; color: white; cursor: pointer; }
        button:hover { background: #444; }
        .gallery { display: flex; flex-wrap: wrap; gap: 15px; padding: 20px; }
        .icon-card { text-align: center; background: #333; padding: 10px; border-radius: 8px; }
        .icon-card img { display: block; max-width: 232px; height: auto; margin-bottom: 5px; }
        .icon-card span { font-size: 12px; color: #bbb; }
        .more { padding: 0 20px 30px; }
    </style>
</head>
<body>

    <div class="search">
        <input id="q" type="search" placeholder="Search: a number, words on the icon, what's on it (flag, eye, starcraft...), or a color" autofocus>
        <label class="nsfw-toggle"><input id="nsfw" type="checkbox"> Show NSFW</label>
        <button id="all" type="button">Browse all</button>
        <p id="count"><?php echo number_format(count($ids)); ?> icons. Type to search them by number, the words on them, what they show, or their colors, using <a href="ik0ns.csv">ik0ns.csv</a>, which anyone can download and use (<a href="/icon-index/">how</a>).</p>
    </div>

    <div class="gallery" id="gallery"></div>
    <div class="more"><button id="more" type="button" style="display: none">Show more</button></div>

    <script>
    // Nothing is shown until you search (or pick Browse all), and then only a page of results at
    // a time, so opening the gallery doesn't load thousands of images at once.
    var ids = <?php echo json_encode($ids); ?>;
    var nsfwIds = <?php echo json_encode($nsfw); ?>;
    (function () {
        var PAGE = 200;
        var isNsfw = {};
        nsfwIds.forEach(function (id) { isNsfw[id] = true; });
        var info = {};
        var words = {};
        var input = document.getElementById('q');
        var showNsfw = document.getElementById('nsfw');
        var count = document.getElementById('count');
        var gallery = document.getElementById('gallery');
        var more = document.getElementById('more');
        var intro = count.innerHTML;
        var browsing = false;
        var results = [];
        var shown = 0;

        function parseCsv(text) {
            var rows = [], row = [], field = '', quoted = false;
            for (var i = 0; i < text.length; i++) {
                var c = text[i];
                if (quoted) {
                    if (c === '"') {
                        if (text[i + 1] === '"') { field += '"'; i++; } else { quoted = false; }
                    } else { field += c; }
                } else if (c === '"') { quoted = true; }
                else if (c === ',') { row.push(field); field = ''; }
                else if (c === '\n' || c === '\r') {
                    if (c === '\r' && text[i + 1] === '\n') { i++; }
                    row.push(field); field = ''; rows.push(row); row = [];
                } else { field += c; }
            }
            if (field || row.length) { row.push(field); rows.push(row); }
            return rows;
        }

        function split(text) {
            return text.toLowerCase().split(/\s+/).map(function (w) {
                return w.replace(/^["'.,!?:;()\-*]+|["'.,!?:;()\-*]+$/g, '');
            }).filter(function (w) { return w.length > 0; });
        }

        // Every query word has to start one of the icon's words: "eye" finds "eyes", "red" doesn't find "tired".
        function matches(id, query, queryWords) {
            if (/^\d+$/.test(query)) { return id.indexOf(query) === 0; }
            var own = words[id];
            if (!own || queryWords.length === 0) { return false; }
            return queryWords.every(function (q) {
                return own.some(function (w) { return w.indexOf(q) === 0; });
            });
        }

        function card(id) {
            var div = document.createElement('div');
            div.className = 'icon-card';
            var tip = ['Icon ' + id];
            var about = info[id];
            if (about) {
                if (about.text) { tip.push('"' + about.text + '"'); }
                if (about.tags) { tip.push(about.tags.split(';').join(', ')); }
                if (about.colors) { tip.push(about.colors.split(';').join(', ')); }
            }
            div.title = tip.join('\n');
            var img = document.createElement('img');
            img.src = './' + id + '.png';
            img.alt = (about && (about.text || about.tags.split(';')[0])) || 'Icon ' + id;
            img.loading = 'lazy';
            var label = document.createElement('span');
            label.textContent = id + '.png';
            div.appendChild(img);
            div.appendChild(label);
            return div;
        }

        function showPage() {
            var end = Math.min(shown + PAGE, results.length);
            var fragment = document.createDocumentFragment();
            for (; shown < end; shown++) { fragment.appendChild(card(results[shown])); }
            gallery.appendChild(fragment);
            more.style.display = shown < results.length ? '' : 'none';
            more.textContent = 'Show more (' + (results.length - shown) + ' left)';
        }

        function update() {
            var query = input.value.trim();
            var queryWords = split(query);
            gallery.innerHTML = '';
            shown = 0;
            if (query === '' && !browsing) {
                results = [];
                more.style.display = 'none';
                count.innerHTML = intro;
                return;
            }
            results = ids.filter(function (id) {
                return (!isNsfw[id] || showNsfw.checked) && (query === '' || matches(id, query, queryWords));
            });
            count.textContent = query === ''
                ? 'All ' + results.length + ' icons.'
                : results.length + ' of ' + ids.length + ' icons match.';
            showPage();
        }

        var request = new XMLHttpRequest();
        request.open('GET', 'ik0ns.csv');
        request.onload = function () {
            if (request.status !== 200) { return; }
            parseCsv(request.responseText).forEach(function (row, i) {
                if (i === 0 && row[0] === 'id') { return; }
                var id = (row[0] || '').trim();
                if (!id) { return; }
                info[id] = { text: row[1] || '', tags: row[2] || '', colors: row[3] || '' };
                words[id] = split([info[id].text, info[id].tags.replace(/;/g, ' '), info[id].colors.replace(/;/g, ' ')].join(' '));
            });
            update();
        };
        request.send();

        input.addEventListener('input', function () { browsing = false; update(); });
        showNsfw.addEventListener('change', update);
        document.getElementById('all').addEventListener('click', function () { input.value = ''; browsing = true; update(); });
        more.addEventListener('click', showPage);
    })();
    </script>

</body>
</html>

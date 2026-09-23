<?php
// Define the directory to scan (current directory)
$dir = './';

// Scan the directory for .png files
$files = glob($dir . "*.png");

// Sort files naturally (1, 2, 10 instead of 1, 10, 2)
natsort($files);

// Icons ik0ns.csv tags "nsfw" (id,text,tags,colors; tags separated by semicolons) start out
// hidden, marked here rather than by the script so they never flash up while the page loads.
$nsfw = array();
if (($csv = @fopen($dir . 'ik0ns.csv', 'r')) !== false) {
    while (($row = fgetcsv($csv, 0, ',', '"', '')) !== false) {
        if (isset($row[2]) && in_array('nsfw', array_map('trim', explode(';', strtolower($row[2]))), true)) {
            $nsfw[trim($row[0])] = true;
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
        body { font-family: sans-serif; background: #1a1a1a; color: white; }
        .search { position: sticky; top: 0; background: #1a1a1a; padding: 15px 20px 5px; z-index: 1; }
        .search input[type=search] { width: 100%; max-width: 520px; font-size: 16px; padding: 8px 10px; border-radius: 6px; border: 1px solid #555; background: #262626; color: white; }
        .search p { font-size: 12px; color: #999; margin: 6px 0 0; }
        .search a { color: #8cf; }
        .nsfw-toggle { font-size: 14px; color: #ccc; margin-left: 12px; white-space: nowrap; }
        .gallery { display: flex; flex-wrap: wrap; gap: 15px; padding: 20px; }
        .icon-card { text-align: center; background: #333; padding: 10px; border-radius: 8px; }
        .icon-card.hidden { display: none; }
        img { display: block; max-width: 232px; height: auto; margin-bottom: 5px; }
        span { font-size: 12px; color: #bbb; }
    </style>
</head>
<body>

    <div class="search">
        <input id="q" type="search" placeholder="Search: a number, words on the icon, what's on it (flag, eye, starcraft...), or a color" autofocus>
        <label class="nsfw-toggle"><input id="nsfw" type="checkbox"> Show NSFW</label>
        <p id="count">Every icon is searchable by the words on it, what it shows, and its colors &mdash; from <a href="ik0ns.csv">ik0ns.csv</a>, which anyone can download and use.</p>
    </div>

    <div class="gallery">
        <?php foreach ($files as $file): ?>
            <?php $id = basename($file, '.png'); $isNsfw = isset($nsfw[$id]); ?>
            <div class="icon-card<?php echo $isNsfw ? ' nsfw hidden' : ''; ?>" data-id="<?php echo $id; ?>">
                <img src="<?php echo $file; ?>" alt="Icon" loading="lazy">
                <span><?php echo basename($file); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    // ik0ns.csv: id,text,tags,colors — tags and colors separated by semicolons. The same file
    // Invigoration's icon picker searches with, so a fix there shows up in both.
    (function () {
        var cards = Array.prototype.slice.call(document.querySelectorAll('.icon-card'));
        var cardById = {};
        cards.forEach(function (card) { cardById[card.getAttribute('data-id')] = card; });
        var words = {};
        var input = document.getElementById('q');
        var count = document.getElementById('count');
        var intro = count.innerHTML;

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
            if (!own) { return false; }
            return queryWords.every(function (q) {
                return own.some(function (w) { return w.indexOf(q) === 0; });
            });
        }

        var showNsfw = document.getElementById('nsfw');

        function filter() {
            var query = input.value.trim();
            var queryWords = split(query);
            var shown = 0;
            cards.forEach(function (card) {
                var nsfw = card.className.indexOf('nsfw') >= 0;
                var show = (!nsfw || showNsfw.checked)
                    && (query === '' || matches(card.getAttribute('data-id'), query, queryWords));
                card.className = 'icon-card' + (nsfw ? ' nsfw' : '') + (show ? '' : ' hidden');
                if (show) { shown++; }
            });
            count.innerHTML = query === '' ? intro : shown + ' of ' + cards.length + ' icons match.';
        }

        var request = new XMLHttpRequest();
        request.open('GET', 'ik0ns.csv');
        request.onload = function () {
            if (request.status !== 200) { return; }
            parseCsv(request.responseText).forEach(function (row, i) {
                if (i === 0 && row[0] === 'id') { return; }
                var id = (row[0] || '').trim();
                if (!id) { return; }
                var text = row[1] || '', tags = row[2] || '', colors = row[3] || '';
                words[id] = split([text, tags.replace(/;/g, ' '), colors.replace(/;/g, ' ')].join(' '));
                var card = cardById[id];
                if (card) {
                    var tip = ['Icon ' + id];
                    if (text) { tip.push('"' + text + '"'); }
                    if (tags) { tip.push(tags.split(';').join(', ')); }
                    if (colors) { tip.push(colors.split(';').join(', ')); }
                    card.title = tip.join('\n');
                    card.querySelector('img').alt = text || tags.split(';')[0] || 'Icon';
                }
            });
            filter();
        };
        request.send();
        input.addEventListener('input', filter);
        showNsfw.addEventListener('change', filter);
    })();
    </script>

</body>
</html>

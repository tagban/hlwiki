<?php
declare(strict_types=1);

/** Helpers for the Markdown page files in content/. */
final class Page
{
    /** 'clients.md' -> 'content/clients.md', or null if the name isn't a safe page path. */
    public static function contentPath(string $page): ?string
    {
        $page = ltrim(str_replace('\\', '/', $page), '/');
        if (!preg_match('#^[A-Za-z0-9_-]+(/[A-Za-z0-9_-]+)*\.md$#', $page)) {
            return null;
        }
        return 'content/' . $page;
    }

    /** Splits a file into [front matter including the --- lines, body]. */
    public static function split(string $text): array
    {
        $text = self::normalize($text);
        if (preg_match('/\A(---\n.*?\n---)\n?(.*)\z/s', $text, $m)) {
            return [$m[1], ltrim($m[2], "\n")];
        }
        return ['', $text];
    }

    public static function join(string $front, string $body): string
    {
        $front = trim(self::normalize($front));
        $body = rtrim(self::normalize($body)) . "\n";
        return $front === '' ? $body : "$front\n\n$body";
    }

    public static function validFrontMatter(string $front): bool
    {
        $front = trim(self::normalize($front));
        return (bool) preg_match('/\A---\n.*^title:.*\n---\z/ms', $front);
    }

    public static function title(string $front): string
    {
        if (preg_match('/^title:\s*"((?:[^"\\\\]|\\\\.)*)"/m', $front, $m)) {
            return (string) json_decode('"' . $m[1] . '"');
        }
        if (preg_match('/^title:\s*(.+)$/m', $front, $m)) {
            return trim($m[1], " '");
        }
        return '';
    }

    public static function newFrontMatter(string $title, string $category): string
    {
        $lines = ['---', 'title: ' . self::yamlString($title)];
        if ($category !== '') {
            $lines[] = 'categories: [' . self::yamlString($category) . ']';
        }
        $lines[] = '---';
        return implode("\n", $lines);
    }

    /** 'GLoarbLine Server' -> 'gloarbline-server'; single CamelCase words split like the import did: 'HTTPTunneling' -> 'http-tunneling'. */
    public static function slug(string $title): string
    {
        $t = $title;
        if (!str_contains(trim($t), ' ')) {
            $t = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1-$2', $t);
            $t = preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', (string) $t);
        }
        $t = strtolower(str_replace("'", '', (string) $t));
        return trim((string) preg_replace('/[^a-z0-9]+/', '-', $t), '-');
    }

    /** URL of a content file on the site: content/clients.md -> /clients/ */
    public static function url(string $path): string
    {
        $p = preg_replace('#^content/|(/?_?index)?\.md$#', '', $path);
        return $p === '' ? '/' : "/$p/";
    }

    private static function normalize(string $text): string
    {
        return str_replace(["\r\n", "\r"], "\n", $text);
    }

    private static function yamlString(string $s): string
    {
        return json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

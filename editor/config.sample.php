<?php
/*
 * Copy this file OUTSIDE the website folder, to:
 *   ~/editor-config/hlwiki.com.php
 * and fill in the values. It holds secrets, so it must never be inside hlwiki.com/.
 */
return [
    'site_name' => 'Hotline Wiki',
    'site_url' => 'https://hlwiki.com',
    'logo' => '/hl_wiki_logo.jpg',
    // Where the editor itself is reached. Defaults to site_url + '/editor'. Set it when the
    // editor has its own domain, such as 'https://edit.bnet.cc'.
    // 'editor_url' => 'https://hlwiki.com/editor',
    // The site's stylesheets, loaded from site_url. Defaults to /css/wiki.css and /css/site.css.
    // 'stylesheets' => ['/css/wiki.css', '/css/site.css'],

    'discord' => [
        // Discord Developer Portal > your application > OAuth2
        'client_id' => '',
        'client_secret' => '',
        // Right-click the server icon > Copy Server ID (needs Developer Mode in Discord settings)
        'guild_id' => '',
        // Server Settings > Roles > right-click the role > Copy Role ID
        'role_ids' => [''],
        'role_name' => 'Wiki Contributor',
        'invite' => 'https://discord.gg/vdxJHwzfrN',
    ],

    'github' => [
        // Fine-grained token limited to this one repository, with
        // "Contents: Read and write" and "Pull requests: Read and write".
        'token' => '',
        'repo' => 'tagban/hlwiki',
        'branch' => 'main',
    ],

    // Local testing only (php -S): skip Discord login and record GitHub writes instead of sending them.
    // 'dev_user' => ['id' => '1', 'username' => 'test', 'name' => 'Test', 'avatar' => null, 'token' => '', 'token_expires' => PHP_INT_MAX],
    // 'dry_run' => true,

    'limits' => [
        'edits_per_hour' => 10,
        'image_max_bytes' => 5000000,
        'image_max_width' => 1600,
    ],
];

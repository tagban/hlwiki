# Hotline Wiki

The source for [hlwiki.com](http://hlwiki.com). Every page is a Markdown file in
`content/`. When a change lands on `main`, GitHub builds the site with
[Hugo](https://gohugo.io) and uploads plain HTML to DreamHost.

The site works in any browser, including classic Mac OS browsers over plain HTTP.

## Editing a page

- **On GitHub:** click **Edit on GitHub** at the bottom of any page. GitHub opens
  the file and offers to create a pull request. When it's merged, the site
  updates within a couple of minutes.
- **With Discord:** coming soon. Members of the Hotline Wiki Discord server will
  be able to edit from the site itself.

### Page format

```markdown
---
title: "Hotstuff Client"
categories: ["Hotline"]
nav: "protocol"   # optional: shows the Hotline Protocol links in the sidebar
toc: true         # optional: force the table of contents on or off
---

Normal **Markdown** here. Link to other pages like [Servers](/servers/).
```

| To add | Write |
|---|---|
| A link to another page | `[Clients](/clients/)` (the file name without `.md`) |
| An image | `![Alt text](/images/Name.png)`, with the file in `static/images/` |
| A download | `[Name.sit](/files/Name.sit)` (see *Downloads* below) |
| A Hotline icon | `{{< iconbox "128" >}}` |
| A community icon | `{{< communityicon "2900" >}}` |
| A server banner | `{{< banner "banner_mobius.jpg" >}}` |

The sidebar is `content/sidebar/index.md`, and the Hotline Protocol link box is
`content/navboxes/protocol/index.md`. Edit them like any other page.

## Icons

`static/ik0ns/` is the complete community icon archive (6,525 icons), and
`static/icons/` is the older, smaller official set. Hotline clients load icons
straight from `http://hlwiki.com/ik0ns/<number>.png`, so these URLs must never
change. To add icons, put the PNG files in `static/ik0ns/`. Deploys upload new
icons but never delete icons that are already on the server.

## Downloads

Client and server downloads are too large for git, so they live only on the web
server in `files/`. The deploy never touches that folder. To add one, upload it
with SFTP, then add its size to `data/files.json` so the page can show it.

## Previewing on your computer

```bash
brew install hugo
hugo server
```

Then open http://localhost:1313.

## Deploy setup (one time)

1. In the DreamHost panel, make the site's user an **SSH** user (not SFTP-only).
2. Move the old MediaWiki files out of the site folder. The deploy **deletes
   anything** in the target folder except `files/`, `editor/`, `.well-known/`
   and the icon folders, and it refuses to run while `LocalSettings.php` is there.
3. Create an SSH key pair for deploys and add the public key to
   `~/.ssh/authorized_keys` on DreamHost.
4. In this repository's **Settings → Secrets and variables → Actions**, add:
   - `DEPLOY_SSH_KEY`: the private key
   - `DEPLOY_HOST`: the DreamHost server name
   - `DEPLOY_USER`: the SSH user
   - `DEPLOY_PATH`: the site folder, e.g. `/home/USER/hlwiki.com`
5. Upload the `files/` folder to the same place once.
6. In the DreamHost panel, leave **Force HTTPS** turned off so old browsers can
   still connect.

## Migration

`migration/` holds the full MediaWiki export (all revisions) and the script that
converted it. It was a one-time import and doesn't need to run again. Old links
such as `/index.php/Main_Page` redirect to the new pages through
`static/.htaccess`.

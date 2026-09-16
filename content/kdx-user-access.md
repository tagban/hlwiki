---
title: "KDXUserAccess"
mediawiki:
  title: "KDXUserAccess"
  revisions: 4
  last_edited: "2026-06-10"
  contributors: ["Schala"]
---

# Overview

KDX uses a 16-byte (128-bit) permission bitmask to control access to server operations. Each bit represents a specific privilege. Permissions are inherited from user categories with optional per-user overrides.

## User Account Permissions

### Categories

| Category | Description                          |     |
|----------|--------------------------------------|-----|
| Accounts | Account operations                   |     |
| Admin    | Administrative miscellaneous         |     |
| Chat     | Chat operations                      |     |
| Files    | File operations                      |     |
| News     | News operations                      |     |
| RDP      | Remote desktop operations            |     |
| Voice    | Voice chat operations                |     |
| ?        | unknown/undocumented/needs more info |     |

### Permission bit mask

| Bit | Name | Category | Note |  |
|----|----|----|----|----|
| 10 | Empty trash | Admin |  |  |
| 15 | Await mode / admin query | ? | Needs clarity |  |
| 17 | Create news articles | News |  |  |
| 21 | Delete news articles | News |  |  |
| 31 | Bypass permission checks | Account |  |  |
| 33 | View folder info | Files |  |  |
| 34 | Delete/modify folders | Files |  |  |
| 37 | Create private folders | Files |  |  |
| 38 | Broadcast messages | Admin |  |  |
| 50 | Chat catalog / user management | Chat? | Needs clarity |  |
| 51 | Send/edit chat messages | Chat |  |  |
| 63 | User is banned | Admin? | Is this a user flag? Needs clarity |  |
| 64 | Upload files | Files |  |  |
| 65 | Download files | Files |  |  |
| 71 | Shutdown server | Admin |  |  |
| 78 | Initiate downloads | Files | Needs clarity |  |
| 79 | Rename/delete files | Files |  |  |
| 85 | Upgrade server | Admin | Probably a do-nothing priv unless third party servers want to implement it |  |
| 87 | Screen sharing | RDP |  |  |

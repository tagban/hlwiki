---
title: "Unofficial Protocol Extensions"
categories: ["Development"]
mediawiki:
  title: "Unofficial Protocol Extensions"
  revisions: 1
  last_edited: "2024-01-03"
  contributors: ["Lostarch"]
---

The following is a collection of Hotline protocol extensions discovered or documented in various third party Hotline clients, servers, and trackers. Please note that some of these may clash with other extensions described here, should you wish to implement them. Some are documented, but unused, however.

This page is an active work in progress and is incomplete.

# Hotline Open Protocol Extensions (HOPE)

HOPE is implemented primarily in various distributions of HXD. It offers an advanced layer of security over vanilla Hotline using cryptography.

## HOPE Transaction Types

**File Hash (3808)**

Initiator: Client

| ID   | Field Name       |
|------|------------------|
| 201  | File Name        |
| 202  | File Path        |
| 203  | File Resume Data |
| 3712 | MD5 Hash         |
| 3713 | HAVAL Hash       |
| 3714 | SHA1 Hash        |

## HOPE Transaction Fields

**Session Key (3587)**

**MAC Algorithm (3588)**

Type: String

Can be one of: "HMAC-MD5", "HMAC-SHA1", "MD5", or "SHA1"

**SID (3687)**

**MD5 Hash (3712)**

Type: Binary

**HAVAL Hash (3713)**

Type: Binary

**SHA1 Hash (3714)**

Type: Binary

**Icon cicn (3728)**

**Chat Away (3745)**

Type: Integer

**Server Cipher Algorithm (3777)**

Type: String

Can be one of: "BLOWFISH", "IDEA", "RC4", or "NONE" (0)

**Client Cipher Algorithm (3778)**

Type: String

Can be one of: "BLOWFISH", "IDEA", "RC4", or "NONE" (0)

**Server Cipher Mode (3779)**

**Client Cipher Mode (3780)**

**Server Cipher IVEC (3781)**

Type: Binary

**Client Cipher IVEC (3782)**

Type: Binary

**Server Checksum Algorithm (3783)**

**Client Checksum Algorithm (3784)**

**Server Compression Algorithm (3785)**

Type: String

Can be one of: "GZIP" or "NONE" (0)

**Client Compression Algorithm (3786)**

Type: String

Can be one of: "GZIP" or "NONE" (0)

# Avaraline extensions

Avaraline seems to have additions relating to either custom or animated icons using the GIF format, with a few other additions. HXD has incorporated support for it.

## Avaraline Transaction Types

**Icon list (1861)**

Initiator: Client

**Set icon (1862)**

Initiator: Client

**Get icon (1863)**

Initiator: Client

**Icon change (1864)**

Initiator: Server

**Link login (2048)**

Initiator: Server

**Link join (2049)**

Initiator: Server

**Link leave (2050)**

Initiator: Server

**Link packet (2051)**

Initiator: Server

**Get news unformatted (2149)**

Initiator: Client

**Get user info unformatted (2160)**

Initiator: Client

**Account self modify (2304)**

Initiator: Client

**Permission list (2305)**

Initiator: Client

## Avaraline Transaction Fields

**GIF Icon (768)**

Type: Binary

**GIF List (769)**

Type: Binary

**Offset (793)**

**Limit (794)**

**Count (795)**

**News Limit (800)**

**Search (1024)**

**Color (1280)**

Type: Integer

**Packet (1536)**

**Post (2048)**

**Post ID (2049)**

**Permission Group (2128)**

**Permissions (2129)**

**IP Address (2304)**

# GLoarbLine extensions

GLoarbLine is a derivative of the official Hotline source code which adds minimal HOPE support along with new account permissions and some misc. packets

## GLoarbLine Transaction Types

**Icon change (123)**

**Nickname change (124)**

**Fake red (125)**

**Away (126)**

**myTran_CrazyServer (127)**

**Block download (128)**

**Visibility (129)**

**Admin spector (130)**

**Standard message (131)**

**Edit news article (412)**

## GLoarbLine Transaction Fields

**Icon ID (117)**

**Nickname (118)**

**Fake Red (119)**

**Away (120)**

**Block Download (121)**

**Visible (122)**

**Admin Spector (123)**

**Standard Message (124)**

## GLoarbLine Privilege Bits

GLoarbLine introduces a few new privilege bits in addition to the official privilege bits.

All privs on looks like this.

`In Hex 'FF F3 CF EF FF FF FE 00'`\
`11111111-11110011-11001111-11111111-11111111-11111111-11111110-00000000`

| Bit | Description               | New user default |
|-----|---------------------------|------------------|
| 12  | Show in user list         |                  |
| 11  | Can close chat            |                  |
| 10  | Can create chat           |                  |
| 21  | Can send private messages |                  |
| 20  | Can change own password   |                  |
| 46  | Can fake admin            |                  |
| 45  | Can go away               |                  |
| 44  | Can change nickname       |                  |
| 43  | Can change icon           |                  |
| 42  | Can speak before          |                  |
| 41  | Can refuse chat           |                  |
| 40  | Can block downloads       |                  |
| 54  | Can be invisible          |                  |
| 53  | Can view invisible users  |                  |
| 52  | Can flood                 |                  |
| 51  | Can view own drop box     |                  |
| 50  | Exempt from queue         |                  |
| 49  | Admin spector             |                  |
| 48  | Can post before           |                  |

# PHXD extensions

Catkiller's PHXD server adds a single addition to Hotline's transaction fields to support an IRC bridge, along with supporting HOPE and Avaraline (mostly).

## PHXD Transaction Fields

**Old IRC Nickname (1024)**

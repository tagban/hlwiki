---
title: "HTTPTunneling"
nav: "protocol"
mediawiki:
  title: "HTTPTunneling"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## HTTP Tunneling

Tunneling uses two persistent connections: **POST** for sending and **GET** for receiving.

### Tunneling Header

Every transaction sent via the tunnel is preceded by this 8-byte header:

| Description | Size | Data Code                             |
|-------------|------|---------------------------------------|
| Data code   | 4    | Disconnect (0), Data (1), Padding (2) |
| Data size   | 4    | Size of the following transaction     |

### MIME Type

All HTTP requests for tunneling must use: `Content-Type: hotline/protocol`

---
title: "Protocol Glossary"
nav: "protocol"
categories: ["Hotline"]
mediawiki:
  title: "Protocol Glossary"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Glossary of Terms

### Architecture & Networking

- **Big-Endian (Network Byte Order):** The byte-ordering used by the Hotline protocol where the most significant byte is stored at the smallest memory address.
- **Base Port:** The primary TCP port configured for a server (default is often 5500). All other service ports (+1, +2, +3) are derived from this number.
- **Handshake (TRTP):** The initial "Hotline Connection" packet (`54 52 54 50`) required to establish a session before any transactions can occur.
- **Transaction:** The fundamental unit of communication in Hotline, consisting of a 20-byte header and a list of sub-parameters.

### Data Structures

- **Forks (Data vs. Info):** A concept inherited from the classic Mac OS File System.
  - **Data Fork:** Contains the actual bytes of a file.
  - **Info Fork:** Contains metadata like the File Type, Creator code, and original timestamps.
- **Magic Number:** A unique 4-byte string (e.g., `FILP`, `HTRK`) used to identify the format of a binary data block.
- **NEG (Negated Login):** A basic obfuscation used in User Management (Trans 352) where every character in a login string is bitwise negated (`~char`).

### Components

- **Tracker:** A central registry server that maintains a list of active Hotline servers and their current user counts.
- **Tunneling:** The process of wrapping Hotline transactions inside standard HTTP GET/POST requests to bypass restrictive firewalls.

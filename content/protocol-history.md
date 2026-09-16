---
title: "ProtocolHistory"
nav: "protocol"
categories: ["Hotline"]
mediawiki:
  title: "ProtocolHistory"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Protocol Evolution: 1.23 to 1.9

The Hotline protocol underwent significant internal changes as it scaled from a small hobbyist tool to a professional-grade communication suite.

### The 1.2x Era (Legacy Basics)

The 1.23 protocol was the "foundation." It established the core TRTP handshake and the concept of Big-Endian binary headers.

- **Features:** Basic Chat (Trans 105), Messaging, and flat File lists.
- **Limitations:** Very limited permission sets and no news threading.
- **Key Header:** The early headers were often shorter and lacked the flexibility for complex sub-parameters seen in later versions.

### The 1.5x Era (The "News" Revolution)

Around April 1999, the protocol underwent a major shift, specifically in how News was handled.

- **Introduction of Bundles:** Version 1.5 introduced **Field 323** (News Category List Data), allowing servers to group news into folders (Bundles) rather than just a flat list.
- **The GUID Shift:** This era saw the introduction of Global Unique Identifiers for news categories to prevent conflicts during server synchronization.
- **Banner Support:** Version 1.5 introduced the logic to request banners (Trans 122) via HTTP or binary transfer.

### The 1.8x - 1.9 Era (The Modern Standard)

This version is what most modern clients and your current wiki documentation focus on. It refined the protocol for high-traffic environments.

- **Advanced Permissions:** The [64-bit Access Bitmap](/access-priviledges/) was fully fleshed out, adding granular control over things like "News Delete Category" and "View Drop Boxes."
- **Flattened File Objects (FILP):** The file transfer logic was finalized to support cross-platform metadata (INFO vs DATA forks), allowing Mac and Windows users to exchange files with their respective metadata intact.
- **Enhanced Login (Version 151):** The login sequence was updated to include mandatory Agreement checks (Trans 109/121) and community-driven Banner IDs.

### Comparison Table

| Feature | v1.23 | v1.5 | v1.8/1.9 |
|----|----|----|----|
| **Login Sequence** | Direct | Added Banner Req | Agreement/Handshake Req |
| **News Layout** | Flat Category | Nested Bundles | Full Threading support |
| **Permissions** | Basic (16-bit) | Expanded (32-bit) | Modern (64-bit) |
| **File Transfers** | Raw Stream | Added Resume support | FILP/RFLT Standardized |

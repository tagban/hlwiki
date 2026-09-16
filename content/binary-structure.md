---
title: "BinaryStructure"
nav: "protocol"
mediawiki:
  title: "BinaryStructure"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Flat File Object (FILP)

Transactions 202, 203, 210, and 213 utilize the Flattened File Object.

### Header

- **Format:** 'FILP' (0x46494C50)
- **Version:** 1 (2 bytes)
- **Fork Count:** 2 (Typically INFO and DATA)

### Information Fork (INFO)

| Offset | Size | Description                 |
|--------|------|-----------------------------|
| 0      | 4    | Platform ('AMAC' or 'MWIN') |
| 4      | 4    | Type Signature              |
| 8      | 4    | Creator Signature           |
| 32     | 8    | Create Date                 |
| 40     | 8    | Modify Date                 |
| 50     | Var  | File Name (Max 128 chars)   |

### Data Fork (DATA)

- **Fork Type:** 'DATA' (0x44415441)
- **Data Size:** 4 bytes (Total file size)
- **Content:** Raw binary file data.

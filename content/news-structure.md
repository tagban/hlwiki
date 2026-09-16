---
title: "NewsStructure"
nav: "protocol"
mediawiki:
  title: "NewsStructure"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## News Data Structures

### News Article List Data (Field 321)

This binary field contains the header and nested list of articles for a news category.

| Description           | Size | Note                                      |
|-----------------------|------|-------------------------------------------|
| ID                    | 4    | Category ID                               |
| Article count         | 4    | Number of articles in list                |
| Name size/data        | Var  | Category Name (1-byte size prefix)        |
| Description size/data | Var  | Category Description (1-byte size prefix) |

#### Article Record (Repeatable)

| Description      | Size | Note                               |
|------------------|------|------------------------------------|
| Article ID       | 4    |                                    |
| Time stamp       | 8    | Year (2), MS (2), Sec (4)          |
| Parent ID        | 4    | 0 if root article                  |
| Flags            | 4    |                                    |
| Flavor count     | 2    | Number of MIME types available     |
| Title size/data  | Var  | Article Title (1-byte size prefix) |
| Poster size/data | Var  | Poster Name (1-byte size prefix)   |

### News Category List Data 1.5 (Field 323)

Used for identifying bundles and categories.

| Type | Value | Structure |
|----|----|----|
| Bundle | 2 | Count (2), Name Size (1), Name Data (Var) |
| Category | 3 | Count (2), GUID (Var), Add SN (4), Delete SN (4), Name Size (1), Name Data (Var) |

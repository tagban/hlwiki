---
title: "KDXTransactionFields"
mediawiki:
  title: "KDXTransactionFields"
  revisions: 4
  last_edited: "2026-06-11"
  contributors: ["Schala"]
---

# Fundamental Types

## Primitive types

| Type  | Size   | Description                                     |
|-------|--------|-------------------------------------------------|
| pstr  | varies | A string prefixed by 1 byte denoting its length |
| u8    | 1      | unsigned byte                                   |
| u32be | 4      | 32 bit big endian unsigned integer              |
| u64be | 8      | 64 bit big endian unsigned integer              |

## Data Size

| Offset | Type  | Description       |
|--------|-------|-------------------|
| 0      | u32be | Total size (high) |
| 4      | u32be | Total size (low)  |

# Transaction Field Types

## Download Info

| Offset | Type      | Description                   |
|--------|-----------|-------------------------------|
| 0      | Data Size | Data fork size                |
| 8      | Data Size | Mac resource fork resume size |
| 16     | u64be     | Created timestamp             |
| 24     | u64be     | Modified timestamp            |
| 32     | u64be     | timestamp?                    |
| 40     | u64be     | timestamp?                    |
| 48     | ?         | ?                             |
| 77     | u8        | File name length              |
| 78     | u8        | Comment length                |
| 79     | u8        | Type string length            |
| 80     | pstr      | File name                     |
|        | pstr      | Comment                       |
|        | pstr      | Type string                   |

## Download Request

| Offset | Type      | Description                                            |
|--------|-----------|--------------------------------------------------------|
| 0      | Data Size | Data resume offset                                     |
| 8      | Data Size | Mac resource fork resume offset                        |
| 16     | ?         | flags and options?                                     |
| 23     | u8        | Fork selector (0 = data only, 1 = data + Mac resource) |
| 24     | pstr      | File path                                              |

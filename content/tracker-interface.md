---
title: "TrackerInterface"
nav: "protocol"
mediawiki:
  title: "TrackerInterface"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Tracker Interface

Communication with trackers uses the **HTRK** magic number.

### Client Request

| Description  | Size | Data                |
|--------------|------|---------------------|
| Magic number | 4    | 'HTRK' (0x4854524B) |
| Version      | 2    | 1 (Old) or 2 (New)  |

### Server List Record

Each server in the tracker's reply follows this format:

| Description           | Size | Note               |
|-----------------------|------|--------------------|
| IP address            | 4    |                    |
| IP port number        | 2    | Server Base Port   |
| Number of users       | 2    |                    |
| Name size/data        | Var  | 1-byte size prefix |
| Description size/data | Var  | 1-byte size prefix |

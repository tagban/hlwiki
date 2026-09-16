---
title: "TransactionSequence"
nav: "protocol"
mediawiki:
  title: "TransactionSequence"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Transaction Sequences

### Login Sequence

1.  **Login (107):** Client sends credentials and Version (160).
2.  **Show Agreement (109):** Server sends agreement text.
3.  **Agreed (121):** Client acknowledges acceptance.
4.  **Get User Name List (300):** Client requests the global user list.
5.  **Post-Login:** Client requests File List (200) or News Categories (370).

### Legacy Login (Version \< 151)

If the server version is below 151, the Agreement phase is skipped. The client immediately sends **Set Client User Info (304)** followed by **Get User Name List (300)**.

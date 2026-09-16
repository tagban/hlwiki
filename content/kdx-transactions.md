---
title: "KDXTransactions"
mediawiki:
  title: "KDXTransactions"
  revisions: 2
  last_edited: "2026-06-10"
  contributors: ["Schala"]
---

## Overview

### Initiators

| Initiator | Description |
|-----------|-------------|
| C         | Client      |
| S         | Server      |
| T         | Tracker     |

### Core and authentication transactions

| ID  | Initiator | Description            |
|-----|-----------|------------------------|
| 100 | C         | Login                  |
| 102 | S         | User info update       |
| 103 | S         | User joined            |
| 108 | C         | Proxy keep-alive       |
| 110 | C         | User status update     |
| 111 | S         | User status changed    |
| 112 | C         | Get server banner      |
| 113 | C         | Get server statistics  |
| 115 | C         | Get counter/keep-alive |
| 118 | C         | Update connection flag |

### User transactions

| ID  | Initiator | Description                  |
|-----|-----------|------------------------------|
| 140 | C         | Enable await mode            |
| 141 | C         | Disable await mode           |
| 142 | C         | Get detailed user list       |
| 143 | S         | Detailed user list reply     |
| 150 | C         | User change/ban              |
| 151 | S         | Disconnect notice            |
| 152 | C         | Get user info with transfers |
| 154 | C         | Broadcast message            |
| 170 | C         | Get user list                |
| 172 | C         | Update user list             |
| 174 | C         | Set icon and broadcast       |
| 175 | C         | Trigger server upgrade       |
| 180 | C         | Get cached news articles     |
| 182 | C         | File lookup notify           |
| 183 | S         | Broadcast receive            |
| 184 | S         | User list broadcast trigger  |

### File transactions

| ID  | Initiator | Description              |
|-----|-----------|--------------------------|
| 200 | C         | Create private folder    |
| 201 | C         | Delete private folder    |
| 202 | C         | Get folder info          |
| 204 | C         | Close folder             |
| 207 | C         | Set folder data          |
| 209 | C         | Set folder comment       |
| 211 | C         | Move or rename folder    |
| 214 | C         | Get folder list          |
| 217 | C         | Create public folder     |
| 218 | C         | Get folder info extended |
| 220 | C         | Modify folder            |
| 222 | C         | Get folder members       |

### News transactions

| ID  | Initiator | Description             |
|-----|-----------|-------------------------|
| 300 | C         | Forward news message    |
| 400 | C         | Get news folder listing |
| 402 | C         | Create news folder      |
| 403 | C         | Copy news articles      |
| 405 | C         | Delete news articles    |
| 407 | C         | Move news articles      |
| 409 | C         | Get news article info   |
| 411 | C         | Modify news article     |
| 420 | C and S   | Delete news folder      |
| 421 | C         | Empty trash             |
| 423 | C and S   | Create news article     |

### Chat transactions

| ID  | Initiator | Description              |
|-----|-----------|--------------------------|
| 450 | C         | Start/stop chat catalog  |
| 452 | C         | Get chat subject users   |
| 502 | C and S   | Get chat message         |
| 504 | C and S   | Send chat message        |
| 505 | C and S   | Get chat message list    |
| 507 | C and S   | Post chat message        |
| 508 | C and S   | Delete chat message      |
| 509 | C and S   | Get chat message info    |
| 511 | C and S   | Edit chat message        |
| 512 | C and S   | Get chat subject list    |
| 550 | C and S   | Set chat subject options |

### File transfer transactions

| ID  | Initiator | Description          |
|-----|-----------|----------------------|
| 600 | C         | Initiate download    |
| 601 | C and S   | Delete transfer      |
| 602 | C and S   | Get transfer info    |
| 604 | C and S   | Set transfer info    |
| 605 | C and S   | Get transfer list    |
| 607 | C and S   | Download file        |
| 609 | C and S   | Download file by ref |
| 611 | C and S   | Upload file          |
| 612 | C and S   | Delete uploaded file |

### Remote desktop transactions

| ID  | Initiator | Description          |
|-----|-----------|----------------------|
| 800 | C         | Start screen sharing |
| 801 | C and S   | Stop screen sharing  |
| 802 | C and S   | Get process list     |
| 804 | C and S   | Terminate process    |

### Administrative transactions

| ID   | Initiator | Description              |
|------|-----------|--------------------------|
| 1100 | C         | Create broadcast message |
| 1101 | C and S   | Delete broadcast message |
| 1103 | C and S   | Get broadcast message    |
| 1105 | C and S   | Update broadcast message |
| 1106 | C and S   | Get broadcast list       |

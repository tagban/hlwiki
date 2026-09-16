---
title: "HL ErrorCodes"
nav: "protocol"
categories: ["Hotline"]
mediawiki:
  title: "HL ErrorCodes"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Error Codes

Error codes in the Hotline protocol are 4-byte (32-bit) integers. A value of **0** always indicates "No Error" (Success).

### General Errors

These are commonly returned in the Transaction Header during basic operations.

| Code (Dec) | Constant | Description |
|----|----|----|
| 0 | err_None | Success / No Error. |
| -1 | err_Generic | A non-specific error occurred. |
| 1 | err_NotConnected | The operation requires a connection that is no longer active. |
| 2 | err_Socket | A low-level TCP/IP socket error occurred. |

### Login & Access Errors

These typically occur during Transaction 107 (Login) or when trying to perform restricted actions.

| Code (Dec) | Constant | Description |
|----|----|----|
| 1000 | err_LoginFailed | Invalid username or password. |
| 1001 | err_AlreadyLoggedIn | The user is already connected to the server. |
| 1002 | err_AccessDenied | The user does not have the required [Access Bit](/access-priviledges/) for this action. |
| 1003 | err_UserBanned | The user's IP or account has been banned from the server. |
| 1004 | err_ServerFull | The server has reached its maximum user limit. |

### File & Transfer Errors

Returned during file system transactions (200-213).

| Code (Dec) | Constant | Description |
|----|----|----|
| 2000 | err_FileNotFound | The requested file or folder path does not exist. |
| 2001 | err_FileInUse | The file is currently being accessed by another process. |
| 2002 | err_DiskFull | The server disk is full; upload cannot complete. |
| 2003 | err_TransferFailed | The binary transfer was interrupted or failed checksum. |

### News & Messaging Errors

| Code (Dec) | Constant | Description |
|----|----|----|
| 3000 | err_NewsFull | The news database cannot accept more posts. |
| 3001 | err_MsgRefused | The recipient has the "Refuse Private Messages" flag set. |

## Field ID 100 (Error Text)

Sometimes, a server will return a generic error code in the header but include **Field 104 (Error Text)** in the parameter list. This field contains a human-readable string explaining the error in more detail (e.g., "Maintenance in progress, please try again in 10 minutes").

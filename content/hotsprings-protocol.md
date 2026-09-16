---
title: "Hotsprings Protocol"
toc: true
categories: ["Network Protocols", "Hotline"]
mediawiki:
  title: "Hotsprings Protocol"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Licensing

The Hotline Protocol is the property of **Hotsprings Inc.** and is licensed under the **GPL**. For closed-source development, a commercial license must be negotiated.

## Protocol Overview

Hotline uses a client-server architecture facilitated by a Tracker application. All communication utilizes TCP/IP.

### Port Assignments

The **base port number** is set manually. Related ports are derived as follows:

| Port          | Usage                              |
|---------------|------------------------------------|
| Base port     | Regular transactions               |
| Base port + 1 | Upload/download                    |
| Base port + 2 | HTTP tunneling (transactions)      |
| Base port + 3 | HTTP tunneling (uploads/downloads) |

**Note:** Numeric data is always in **network byte order (big-endian)**.

## Session Initialization

After TCP connection, a TRTP handshake occurs.

### Client Request

| Description     | Size (Bytes) | Data   | Note         |
|-----------------|--------------|--------|--------------|
| Protocol ID     | 4            | 'TRTP' | 0x54525450   |
| Sub-protocol ID | 4            |        | User defined |
| Version         | 2            | 1      | Currently 1  |
| Sub-version     | 2            |        | User defined |

### Server Reply

| Description | Size (Bytes) | Data   | Note         |
|-------------|--------------|--------|--------------|
| Protocol ID | 4            | 'TRTP' |              |
| Error code  | 4            |        | 0 = no error |

## Transactions

Every transaction consists of a header followed by a parameter list.

### Transaction Header

| Description | Size | Note                        |
|-------------|------|-----------------------------|
| Flags       | 1    | Reserved (0)                |
| Is reply    | 1    | Request (0) or reply (1)    |
| Type        | 2    | Requested operation ID      |
| ID          | 4    | Unique ID (must be != 0)    |
| Error code  | 4    | 0 = no error                |
| Total size  | 4    | Total transaction data size |
| Data size   | 4    | Size of data in this part   |

### Parameter Structure

A transaction contains a 2-byte field for the **Number of parameters**, followed by multiple structures:

| Description | Size | Note              |
|-------------|------|-------------------|
| Field ID    | 2    | Unique identifier |
| Field size  | 2    | Data part size    |
| Field data  | Var  | Actual content    |

## Transaction Descriptions

### Login (107)

- **Constant:** myTran_Login
- **Initiator:** Client

**Request Fields:**

- 105: User login
- 106: User password
- 160: Version (Currently 151)

**Reply Fields:**

- 160: Version
- 161/162: Banner ID/Server Name (if Version \>= 151)

### Send Chat (105)

- **Access:** Send Chat (10)
- **Initiator:** Client

**Request Fields:**

- 101: Chat message string
- 109: Chat options (Normal 0, Alternate 1)
- 114: Chat ID (Optional)

### Disconnect User (110)

- **Access:** Disconnect User (22)
- **Initiator:** Client

**Request Fields:**

- 103: User ID
- 113: Options (Ban options)

## Flattened File Object (FILP)

Used for file/folder transfers (Trans 202, 203, 210, 213).

### Flat File Header

- **Format:** 'FILP' (0x46494C50)
- **Fork Count:** 2 (Information Fork and Data Fork)

### Information Fork (INFO)

- **Platform:** 'AMAC' or 'MWIN'
- **Metadata:** Type signature, Creator signature, Create/Modify dates, Name.

### Data Fork (DATA)

- **Data Size:** 4 bytes
- **Content:** Actual file bytes.

## Transaction Fields

Predefined types: **Integer**, **String**, and **Binary**.

| ID  | Constant             | Type    | Note                |
|-----|----------------------|---------|---------------------|
| 101 | myField_Data         | Binary  | General data        |
| 102 | myField_UserName     | String  |                     |
| 103 | myField_UserID       | Integer |                     |
| 105 | myField_UserLogin    | String  |                     |
| 106 | myField_UserPassword | String  |                     |
| 110 | myField_UserAccess   | Binary  | 64-bit bitmap       |
| 114 | myField_ChatID       | Integer |                     |
| 160 | myField_Vers         | Integer | Protocol version    |
| 202 | myField_FilePath     | Binary  | File path structure |

## Access Privileges

Represented as a 64-bit bitmap in Field 110.

| Bit | Name            | Constant         | Type           |
|-----|-----------------|------------------|----------------|
| 0   | Delete File     | myAcc_DeleteFile | folder         |
| 1   | Upload File     | myAcc_UploadFile | folder/general |
| 9   | Read Chat       | myAcc_ReadChat   | general        |
| 10  | Send Chat       | myAcc_SendChat   | general        |
| 22  | Disconnect User | myAcc_DisconUser | general        |
| 32  | Broadcast       | myAcc_Broadcast  | general        |

## Tracker Interface

### Client to Tracker

**Request:**

- Magic Number: 'HTRK' (4 bytes)
- Version: 1 or 2 (2 bytes)

**Record Structure:**

- IP Address (4), Port (2), User Count (2), Name Size (1), Name (Var).

## HTTP Tunneling

Uses POST for sending and GET for receiving.

- **MIME:** `hotline/protocol`
- **Data Header:** 4-byte Code (0=Disconnect, 1=Data, 2=Padding), 4-byte Size.

## Global Server

Servers register with a unique name and password to the Global Server to receive a **Server ID**.

### Fields Stored

- Server ID, Name, Password, Alias, Description, IP, Status Flags, User Count.

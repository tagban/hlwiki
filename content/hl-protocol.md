---
title: "HL Protocol"
nav: "protocol"
categories: ["Hotline", "Network Protocols"]
toc: false
mediawiki:
  title: "HL Protocol"
  revisions: 4
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

# Hotline Network Protocol – Version 1.9

**Property of Hotsprings Inc.** Publicly licensed under the GPL

## Introduction

Hotline client is an application providing user interface for end-user services (chat, messaging, file services and other). Hotline server provides services and facilitates communication between all clients.

### Port Assignments

Additional port numbers are determined by using the **base port number**:

| Port          | Usage                                   |
|---------------|-----------------------------------------|
| Base port     | Regular transactions                    |
| Base port + 1 | Upload/download                         |
| Base port + 2 | HTTP tunneling for regular transactions |
| Base port + 3 | HTTP tunneling for uploads/downloads    |

**Note:** Numeric data is always in network byte order (big-endian).

## Technical Documentation Index

### Core Protocol

- [Transactions](/transactions/) – The master list of all 411 Transaction IDs and their constants.
- [TransactionFields](/transaction-fields/) – Detailed list of Field IDs (100–337) and data types.
- [AccessPriviledges](/access-priviledges/) – The 64-bit access privilege bitmap breakdown.
- [TransactionSequence](/transaction-sequence/) – Logic for Login and Invite-to-Chat sequences.

### Data Structures

- [BinaryStructure](/binary-structure/) – Details on the Flattened File Object (FILP) and Resume data (RFLT).
- [NewsStructure](/news-structure/) – Binary headers for news categories, articles, and posters.

### External Interfaces

- [TrackerInterface](/tracker-interface/) – How servers register with and clients query the Tracker (HTRK).
- [HTTPTunneling](/http-tunneling/) – Encapsulation logic for GET/POST proxy traversal.
- [GlobalServerTransactions](/global-server-transactions/) – Management and querying of the Global Server database.

## Session Initialization

After establishing TCP connection, both client and server start the TRTP handshake.

### Client Request

| Description     | Size | Data   | Note         |
|-----------------|------|--------|--------------|
| Protocol ID     | 4    | 'TRTP' | 0x54525450   |
| Sub-protocol ID | 4    |        | User defined |
| Version         | 2    | 1      | Currently 1  |
| Sub-version     | 2    |        | User defined |

### Server Reply

| Description | Size | Data   | Note         |
|-------------|------|--------|--------------|
| Protocol ID | 4    | 'TRTP' | 0x54525450   |
| Error code  | 4    |        | 0 = no error |

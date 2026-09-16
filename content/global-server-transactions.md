---
title: "GlobalServerTransactions"
nav: "protocol"
mediawiki:
  title: "GlobalServerTransactions"
  revisions: 3
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Global Server Transactions

Used for managing global server accounts and database queries.

| ID    | Type           | Initiator | Description                        |
|-------|----------------|-----------|------------------------------------|
| 1.2.1 | Server Login   | Server    | Initial login to global database   |
| 1.2.2 | Update Info    | Server    | Updates alias, desc, user count    |
| 1.2.3 | Delete Account | Client    | Administrator only                 |
| 1.2.4 | Rate Server    | Client    | Submits server rating              |
| 1.2.5 | Query DB       | Client    | Search string/classification query |
| 1.2.6 | Get Info       | Client    | Detailed server stats by ID        |

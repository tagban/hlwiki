---
title: "TransactionFields"
nav: "protocol"
mediawiki:
  title: "TransactionFields"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Transaction Fields

Hotline fields utilize three data types: **Integer**, **String**, and **Binary**. Integers are network-byte order (Big-Endian).

| ID | Constant | Type | Description |
|----|----|----|----|
| 100 | myField_ErrorText |  |  |
| 101 | myField_Data | Binary | Generic data/Chat text |
| 102 | myField_UserName | String |  |
| 103 | myField_UserID | Integer |  |
| 104 | myField_UserIconID | Integer |  |
| 105 | myField_UserLogin | String |  |
| 106 | myField_UserPassword | String |  |
| 107 | myField_RefNum | Integer | File transfer reference |
| 108 | myField_TransferSize | Integer | Size in bytes |
| 109 | myField_ChatOptions | Integer | 0=Normal, 1=Admin |
| 110 | myField_UserAccess | Binary | 64-bit Access Bitmap |
| 112 | myField_UserFlags | Integer | Away(1), Admin(2), RefusePM(4), RefuseChat(8) |
| 113 | myField_Options | Integer | PM Options Bitmap |
| 114 | myField_ChatID | Integer |  |
| 115 | myField_ChatSubject | String |  |
| 116 | myField_WaitingCount | Integer | Queue position |
| 160 | myField_Vers | Integer | Protocol version (e.g., 151) |
| 200 | myField_FileNameWithInfo | Binary | Struct: Type, Creator, Size, Name |
| 201 | myField_FileName | String |  |
| 202 | myField_FilePath | Binary | Folder path array |
| 203 | myField_FileResumeData | Binary | Struct: RFLT Format |
| 208 | myField_FileCreateDate | Binary | Struct: Year, MS, Sec |
| 209 | myField_FileModifyDate | Binary | Struct: Year, MS, Sec |
| 300 | myField_UserNameWithInfo | Binary | Struct: ID, Icon, Flags, Name |
| 321 | myField_NewsArtListData | Binary | Detailed News Header struct |
| 325 | myField_NewsPath | Binary |  |
| 326 | myField_NewsArtID | Integer |  |
| 333 | myField_NewsArtData | Binary | Article content |
| 337 | myField_NewsArtRecurseDel | Integer | 0=Single, 1=Recursive |

---
title: "AccessPriviledges"
nav: "protocol"
mediawiki:
  title: "AccessPriviledges"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Access Privileges

Access privileges are represented as a 64-bit bitmap. Privileges are categorized as **General**, **Folder**, or **Bundle**.

| Bit | Name                   | Constant               | Type            |
|-----|------------------------|------------------------|-----------------|
| 0   | Delete File            | myAcc_DeleteFile       | folder          |
| 1   | Upload File            | myAcc_UploadFile       | folder, general |
| 2   | Download File          | myAcc_DownloadFile     | folder, general |
| 3   | Rename File            | myAcc_RenameFile       |                 |
| 4   | Move File              | myAcc_MoveFile         |                 |
| 5   | Create Folder          | myAcc_CreateFolder     | folder          |
| 6   | Delete Folder          | myAcc_DeleteFolder     | folder          |
| 7   | Rename Folder          | myAcc_RenameFolder     |                 |
| 8   | Move Folder            | myAcc_MoveFolder       |                 |
| 9   | Read Chat              | myAcc_ReadChat         | general         |
| 10  | Send Chat              | myAcc_SendChat         | general         |
| 11  | Open Chat              | myAcc_OpenChat         |                 |
| 12  | Close Chat             | myAcc_CloseChat        |                 |
| 13  | Show in List           | myAcc_ShowInList       |                 |
| 14  | Create User            | myAcc_CreateUser       |                 |
| 15  | Delete User            | myAcc_DeleteUser       |                 |
| 16  | Open User              | myAcc_OpenUser         |                 |
| 17  | Modify User            | myAcc_ModifyUser       |                 |
| 18  | Change Own Password    | myAcc_ChangeOwnPass    |                 |
| 19  | Send Private Message   | myAcc_SendPrivMsg      |                 |
| 20  | News Read Article      | myAcc_NewsReadArt      | bundle, general |
| 21  | News Post Article      | myAcc_NewsPostArt      | general, bundle |
| 22  | Disconnect User        | myAcc_DisconUser       | general         |
| 23  | Cannot be Disconnected | myAcc_CannotBeDiscon   |                 |
| 24  | Get Client Info        | myAcc_GetClientInfo    | general         |
| 25  | Upload Anywhere        | myAcc_UploadAnywhere   |                 |
| 26  | Any Name               | myAcc_AnyName          | general         |
| 27  | No Agreement           | myAcc_NoAgreement      |                 |
| 28  | Set File Comment       | myAcc_SetFileComment   | folder          |
| 29  | Set Folder Comment     | myAcc_SetFolderComment | folder          |
| 30  | View Drop Boxes        | myAcc_ViewDropBoxes    |                 |
| 31  | Make Alias             | myAcc_MakeAlias        | folder          |
| 32  | Broadcast              | myAcc_Broadcast        | general         |
| 33  | News Delete Article    | myAcc_NewsDeleteArt    | bundle          |
| 34  | News Create Category   | myAcc_NewsCreateCat    | bundle          |
| 35  | News Delete Category   | myAcc_NewsDeleteCat    | bundle          |
| 36  | News Create Folder     | myAcc_NewsCreateFldr   | bundle          |
| 37  | News Delete Folder     | myAcc_NewsDeleteFldr   | bundle          |

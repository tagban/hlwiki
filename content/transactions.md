---
title: "Transactions"
nav: "protocol"
mediawiki:
  title: "Transactions"
  revisions: 4
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

| ID  | Type                    | Initiator     | Constant                    |
|-----|-------------------------|---------------|-----------------------------|
| 100 | Error                   | ?             | myTran_Error                |
| 101 | Get messages            | Client        | myTran_GetMsgs              |
| 102 | New message             | Server        | myTran_NewMsg               |
| 103 | Old post news           | Client        | myTran_OldPostNews          |
| 104 | Server message          | Server        | myTran_ServerMsg            |
| 105 | Send chat               | Client        | myTran_ChatSend             |
| 106 | Chat message            | Server        | myTran_ChatMsg              |
| 107 | Login                   | Client        | myTran_Login                |
| 108 | Send instant message    | Client        | myTran_SendInstantMsg       |
| 109 | Show agreement          | Server        | myTran_ShowAgreement        |
| 110 | Disconnect user         | Client        | myTran_DisconnectUser       |
| 111 | Disconnect message      | Server        | myTran_DisconnectMsg        |
| 112 | Invite to a new chat    | Client        | myTran_InviteNewChat        |
| 113 | Invite to chat          | Client/Server | myTran_InviteToChat         |
| 114 | Reject chat invite      | Client        | myTran_RejectChatInvite     |
| 115 | Join chat               | Client        | myTran_JoinChat             |
| 116 | Leave chat              | Client        | myTran_LeaveChat            |
| 117 | Notify chat user change | Server        | myTran_NotifyChatChangeUser |
| 118 | Notify chat delete user | Server        | myTran_NotifyChatDeleteUser |
| 119 | Notify chat subject     | Server        | myTran_NotifyChatSubject    |
| 120 | Set chat subject        | Client        | myTran_SetChatSubject       |
| 121 | Agreed                  | Client        | myTran_Agreed               |
| 122 | Server banner           | Server        | myTran_ServerBanner         |
| 200 | Get file name list      | Client        | myTran_GetFileNameList      |
| 202 | Download file           | Client        | myTran_DownloadFile         |
| 203 | Upload file             | Client        | myTran_UploadFile           |
| 204 | Delete file             | Client        | myTran_DeleteFile           |
| 205 | New folder              | Client        | myTran_NewFolder            |
| 206 | Get file info           | Client        | myTran_GetFileInfo          |
| 207 | Set file info           | Client        | myTran_SetFileInfo          |
| 208 | Move file               | Client        | myTran_MoveFile             |
| 209 | Make file alias         | Client        | myTran_MakeFileAlias        |
| 210 | Download folder         | Client        | myTran_DownloadFldr         |
| 211 | Download info           | Server        | myTran_DownloadInfo         |
| 212 | Download banner         | Client        | myTran_DownloadBanner       |
| 213 | Upload folder           | Client        | myTran_UploadFldr           |
| 300 | Get user name list      | Client        | myTran_GetUserNameList      |
| 301 | Notify user change      | Server        | myTran_NotifyChangeUser     |
| 302 | Notify delete user      | Server        | myTran_NotifyDeleteUser     |
| 303 | Get client info text    | Client        | myTran_GetClientInfoText    |
| 304 | Set client user info    | Client        | myTran_SetClientUserInfo    |
| 350 | New user                | Client        | myTran_NewUser              |
| 351 | Delete user             | Client        | myTran_DeleteUser           |
| 352 | Get user                | Client        | myTran_GetUser              |
| 353 | Set user                | Client        | myTran_SetUser              |
| 354 | User access             | Server        | myTran_UserAccess           |
| 355 | User broadcast          | Client/Server | myTran_UserBroadcast        |
| 370 | Get news category list  | Client        | myTran_GetNewsCatNameList   |
| 371 | Get news article list   | Client        | myTran_GetNewsArtNameList   |
| 380 | Delete news item        | Client        | myTran_DelNewsItem          |
| 381 | New news folder         | Client        | myTran_NewNewsFldr          |
| 382 | New news category       | Client        | myTran_NewNewsCat           |
| 400 | Get news article data   | Client        | myTran_GetNewsArtData       |
| 410 | Post news article       | Client        | myTran_PostNewsArt          |
| 411 | Delete news article     | Client        | myTran_DelNewsArt           |

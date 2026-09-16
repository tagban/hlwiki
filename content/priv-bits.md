---
title: "Priv Bits"
categories: ["Development"]
mediawiki:
  title: "Priv Bits"
  revisions: 1
  last_edited: "2024-01-03"
  contributors: ["Lostarch"]
---

Compiled by Virtual1, from multiple sources

Privs are stored in the *UserData* file located in the users folder. All the priv data is stored in binary 1's & 0's taking up 8 bytes (4th byte thru 11th). The bytes are the 4th thru 7th Bytes in the Userdata file, space for up to 64 privs. Only the first six bytes are currently used.

In the following description 1 denotes the highest bit (leftmost) in a byte. If priv \#1 is the only one enabled, then the byte is chr(128).

All privs on looks like this.

`In Hex 'FF F3 CF FF FF 80 00 00'`\
`11111111-11110011-11001111-11111111-11111111-10000000-00000000-00000000`

| Bit | Description                | New user default |
|-----|----------------------------|------------------|
| 7   | Can Delete Files           |                  |
| 6   | Can Upload Files           | yes              |
| 5   | Can Download Files         | yes              |
| 4   | Can Rename Files           |                  |
| 3   | Can Move Files             |                  |
| 2   | Can Create Folders         |                  |
| 1   | Can Delete Folders         |                  |
| 0   | Can Rename Folders         |                  |
| 15  | Can Move Folders           |                  |
| 14  | Can Read Chat              | yes              |
| 13  | Can Send Chat              | yes              |
| 12  | Reserved (not used)        |                  |
| 11  | Reserved (not used)        |                  |
| 10  | Reserved (not used)        |                  |
| 9   | Can Create Users           |                  |
| 8   | Can Delete Users           |                  |
| 23  | Can Read Users             |                  |
| 22  | Can Modify Users           |                  |
| 21  | Reserved (not used)        |                  |
| 20  | Reserved (not used)        |                  |
| 19  | Can read news              | yes              |
| 18  | Can Post News              | yes              |
| 17  | Can Disconnect Users       |                  |
| 16  | Cannot be Disconnected     |                  |
| 31  | Can Get User Info          |                  |
| 30  | Can Upload Anywhere        |                  |
| 29  | Can Use Any Name           | yes              |
| 28  | Don't Show Agreement       |                  |
| 27  | Can Comment Files          |                  |
| 26  | Can Comment Folders        |                  |
| 25  | Can View DropBoxes         |                  |
| 24  | Can Make Aliases           |                  |
| 39  | Can Broadcast              |                  |
| 38  | Can delete news articles   |                  |
| 37  | Can create news categories |                  |
| 36  | Can delete news categories |                  |
| 35  | Can create news folders    |                  |
| 34  | Can delete news folders    |                  |
| 33  | Can upload folders         |                  |
| 32  | Can download folders       |                  |
| 47  | Send messages              |                  |
| 46  | Reserved (not used)        |                  |
| 45  | Reserved (not used)        |                  |
| 44  | Reserved (not used)        |                  |
| 43  | Reserved (not used)        |                  |
| 42  | Reserved (not used)        |                  |
| 41  | Reserved (not used)        |                  |
| 40  | Reserved (not used)        |                  |

UserData file, 734 bytes total:

| Position | Default | What |
|----|----|----|
| 1-2 | 0001 | short(PrefsVersion) |
| 3-4 | 0000 | short(minimum server version that can access correctly |
| 5-12 | 20700C2000800000 | priv bits (above) |
| 13-526 | (zeros) | reserved for future expansion |
| 527-530 | 00000005 | long(nickname length) |
| 531-562 | 'Guest' | string(nickname) |
| 563-662 | (zeros) | reserved for future expansion |
| 663-666 | 00000005 | long(account name length) |
| 667-698 | 'guest' | string(account name) |
| 699-702 | 00000000 | long(password length) |
| 703-734 | (blank) | string(password) |

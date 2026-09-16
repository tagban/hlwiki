---
title: "Carracho protocol"
mediawiki:
  title: "Carracho protocol"
  revisions: 4
  last_edited: "2025-05-02"
  contributors: ["Schala_ghost"]
---

# Protocol overview

Carracho was a non-compatible Hotline clone built around Apple and Mac exclusivity between 1998 and 2004 by Jorn and Mirko Hartmann. It provides everything the [Hotline protocol](/protocol/) offers, but in its own way: chatrooms, file sharing, threaded news, etc. The only real features it provides that differs from Hotline are custom user icons and slightly better password encryption. As such, since Macs during this time were 68k/PowerPC, the protocol operates on big endian.

# Passwords

Instead of XORing every character of a password by 255, Carracho encrypts the password marginally better with modulo and XORing, with the additional step of hashing the encrypted password with the MD5 algorithm.

`void EncryptDecryptPassword(char *ioText)`\
`{`\
`   if (!ioText)`\
`       return;`\
\
`   size_t length = strlen(ioText);`\
`   for (size_t i = 0; i < length; ++i)`\
`       switch (i % 3)`\
`       {`\
`           case 0:`\
`               ioText[i] ^= 0x88;`\
`               break;`\
`           case 1:`\
`               ioText[i] ^= 0x44;`\
`               break;`\
`           case 2:`\
`               ioText[i] ^= 0x12;`\
`               break;`\
`       }`\
`}`

# Handshake

Upon connecting to a server, the client sends over TCP the ASCII text "TCPCARRACHO", optionally appended by a 16-bit value of 1. The server checks this and replies with "TCPCARRACHO", followed by a 16-bit value of 2.

---
title: "KDX Protocol"
mediawiki:
  title: "KDX Protocol"
  revisions: 14
  last_edited: "2025-05-13"
  contributors: ["Schala_ghost"]
---

# Protocol Overview

Haxial KDX was a non-compatible Hotline clone developed between 2001 and 2005 by Australian-based company Haxial Software Pty. Ltd. It provides everything the [Hotline protocol](/protocol/) does along with its own array of features such as remote desktop administration, detailed file access, user account classes, custom user icons, multiple public chat rooms, IRC bridging, server categories and accounts on the tracker, and voice chat. Most notable is its extensive use of mixing known standard cryptographic hashes and encryption algorithms of the time ([MD5](https://en.wikipedia.org/wiki/MD5), [CRC32](https://en.wikipedia.org/wiki/Computation_of_cyclic_redundancy_checks), [Twofish](https://en.wikipedia.org/wiki/Twofish), etc.) as well as some of its own proprietary algorithms. KDX's news system is a hybrid of Hotline's threaded news system and it's classic bulletin board-style news feed: a threaded news system where each category is its own bulletin board-style feed.

# Algorithms

The following is a list of algorithms discovered in KDX. While I do my best to document and reimplement the algorithms, their purposes, until KDX is fully reimplemented, are only speculation and are subject to clarification as I progress in development. - Schala

## Multipurpose

I found this recurring [LCG](https://en.wikipedia.org/wiki/Linear_congruential_generator)-based XOR encryption algorithm used in various areas of the network code with varying constants passed to it.

`void DataCrypt(void *ioData, size_t inDataSize, unsigned inInit, unsigned inMultiplier, unsigned inAddend)`\
`{`\
`   if (!ioData || !inDataSize || inDataSize & 3)`\
`       return;`\
\
`   auto data = reinterpret_cast<uint32_t *>(ioData);`\
`   auto numBlocks = inDataSize >> 2;`\
\
`   for (auto i = 0; i < numBlocks; i++)`\
`   {`\
`       data[i] ^= htonl(inInit);`\
`       inInit = inInit * inMultiplier + inAddend;`\
`   }`\
`}`

## TCP (client/server based) packets

An alternative C implementation of this can be found [here](https://aluigi.altervista.org/papers/kdxalgo.h). However, I've found the returning key value to be garbage. The initial key should be retained.

- The function takes a 32-bit key, a data buffer, and its length in bytes
- The data length is converted from bytes to 32-bit words (dividing by 4, or bit shifting right by 2)
- The data buffer is treated as an array of 32-bit integers

For each 32-bit word:

- The key is updated by left-shifting and adding a constant (0x4878)
- The data word is XORed with the key (after converting byte order if needed)

The final key value is returned.

`void TCPPacketCrypt(unsigned inInit, void *ioData, size_t inDataSize)`\
`{`\
`   if (!ioData || !inDataSize || inDataSize & 3)`\
`       return;`\
\
`   auto data = reinterpret_cast<uint32_t *>(ioData);`\
`   auto numBlocks = inDataSize >> 2;`\
\
`   for (auto i = 0; i < numBlocks; i++)`\
`   {`\
`       inInit = (inInit << 1) + 0x4878;`\
`       data[i] ^= htonl(inInit);`\
`   }`\
`}`

## UDP (tracker-based) packets

Tracker packet encryption has been observed to be encrypted with the aforementioned multipurpose LCG-XOR algorithm, but with these constants fed to it:

Initial value: `0xA5A16C4A`

Multiplier: `0x41D28485`

Addend: `12843`

# Anti-piracy DRM

I haven't fully figured out how this is used, but in my network data mining, I've noticed some decrypted network packets include random segments of writing. When I decompiled the client and server, I found what appear to be short stories embedded within the executables. After some research, it became apparent that segments of these writings are applied to network packets as part of some anti-piracy measure. As Haxial is long gone, the copyrights on these are in limbo. The copyright disclaimers, as far as I know, should be kept intact, lest it break network packet integrity. Further research is ongoing.

`This program including the following story is Copyright 2003 Haxial Software Pty Ltd and unauthorized reproduction is strictly PROHIBITED.`\
`Haxial and KDX are trademarks of Haxial Software and unauthorized use is strictly PROHIBITED.`\
\
`-- Sale of the Cesspool --`\
`"If you punch Cyclops in the eye, how many eyes does he have left?", asked Gresh Rock, host of Sale of the Cesspool, in front of a packed athenaeum of ogres.`\
`"Was he punched in the left or right eye?", queried contestant number 1.`\
`Contestant number 3 interrupted. "My buzzer's not working!!", he yelled furiously as he bashed his red buzzer repeatedly.  A team of goblins rushed on stage and before a moment had elapsed they had disassembled and reassembled the buzzer.`\
`"Play on!", announced Gresh Rock excitedly.  Contestant 3 beat his opponents as he slammed his fist down onto his newly-fixed buzzer.  BOOOOOOM!!! A thunderous sound rocked the stage as Contestant 3's booth exploded!  The audience burst into riotous laughter, shoving and elbowing each other.`\
`"Anyone else want to jump in here?" asked Gresh Rock.`\
`"I know the answer!", said contestant number 2.`\
`"Well press your buzzer."`\
`Contestant 2 tentatively pressed his buzzer.  BOOOOOOM!!!  The audience also exploded, but into even more riotous laughter.`\
`Gresh Rock looked expectantly to the last remaining contestant.`\
`"umm I don't know", said the last contestant, looking back and forth between his buzzer and the other ex-contestants.`\
`"Wrong answer!"  BOOOOOOM!!!`\
\
`Copyright 2003 Haxial Software. All rights reserved. Unauthorized reproduction prohibited.`

`The following story is Copyright 2004 Haxial Software Pty Ltd and unauthorized reproduction is strictly PROHIBITED.`\
`Haxial and KDX are trademarks of Haxial Software and unauthorized use is strictly PROHIBITED.`\
\
`Gresh Rock, former host of "Sale of the Cesspool", new host of "Who Wants To Be A Maggot?", looked intently at his single contestant, Drohgar Bonemuncher, and asked,`\
`"Question 15, for 1 MILLION juicy eyeballs, your hunting partner is being charged by a giant gormeld, do you, A, wait until the gormeld skewers your partner, then spear the gormeld while it is distracted eating your partner, B, viciously insult the gormeld until it stops charging your partner and chases you instead, C, valiantly tackle the gormeld to the ground, allowing your partner to escape, or D, pull up a log to sit on and watch the entertainment?"`\
\
`Drohgar tilted his head and looked upwards while thinking with half-closed eyes and pursed lips.  He sat in a roughly-constructed greasy wooden chair on a dais with Gresh Rock, in the middle of an amphitheater packed with leering ogres.  After a moment, Drohgar answered simply, "A?".`\
\
`Gresh Rock asked incredulously, "Is that your final answer?!", as if the answer were so obviously wrong, in an attempt to subvert Drohgar.  Drohgar looked doubtful, but stayed with his original answer anyway.`\
\
`Gresh Rock peered down at the question card in his hands, held close to his chest.  "That answer is...", he paused and took a swill from his tankard, meanwhile beads of sweat rolled down Drohgar's worried forehead, "...CORRECT!!  Only a true maggot could be that opportunistic!  You've won 1 MILLION eyeballs!"  Very dramatic music involving drums and a giant cymbal crashed and rumbled.`\
\
`From somewhere above, slimy eyeballs rained down upon Drohgar, and he greedily snatched them up from wherever they landed, his shoulders, his lap, the floor, and stuffed them into his mouth excitedly.  He chewed haphazardly, not bothering to fully close his mouth, only swallowing was important.`\
\
`A ratty-looking wizard stepped up onto the dais and started muttering incantations while waving his crooked wand in the direction of Drohgar.  Drohgar leaped up from his chair in surprise and exclaimed, "What?! Stop! NO!!".  But it was too late, his skin was already rapidly becoming white, and his arms and were merging into his torso, and his legs into each other.  Within moments he was transformed into a giant ogre-sized slimy translucent white maggot, with a black head.  He wriggled and shrieked in fury at his predicament.  The audience roared with laughter, elbowing and slapping each other on the back.`\
\
`The wizard had vanished, so seeing nothing else to do, Drohgar wriggled surprisingly quickly towards the audience, intent on eating his way through a few bodies.  The audience immediately guessed his intentions and a great noisy commotion and fight broke out as the ogres scrambled and trod on and kicked each other in their desperate attempt to escape the giant maggot.  Seizing the opportunity, the maggot propelled himself headfirst into the middle of the audience.`\
\
`Copyright 2004 Haxial Software. All rights reserved. Unauthorized reproduction prohibited.`

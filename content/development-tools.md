---
title: "DevelopmentTools"
nav: "protocol"
categories: ["Hotline", "Development"]
mediawiki:
  title: "DevelopmentTools"
  revisions: 2
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Development Tools & Libraries

Building for the Hotline protocol in the modern era requires libraries that can handle Big-Endian binary structures and the TRTP handshake. Below are the recommended tools and resources for current developers.

### Modern Libraries

#### Python (Discord-Hotline Bridge)

The following Python script provides a stable foundation for a Discord-to-Hotline relay. It utilizes `asyncio` for non-blocking socket communication and `discord.py` for the bot interface.

- **Source:** [View Python Bridge Source](/hl-protocol-bridge-script/)
- **Key Features:** Guest login (0x6b), Plain-text relay, and Webhook support.

#### Go-Hotline (ghotline)

A modern implementation of the Hotline protocol written in Go. Excellent for building high-performance trackers or automated bots.

- **Capability:** Full support for Transaction 107 (Login) and 200 (File Listing).
- **Use Case:** Server monitoring and high-speed indexing.

#### Hotline SDK (C/C++)

The original logic for many classic clients. While dated, these headers provide the exact struct definitions for `FILP` and `RFLT`.

- **Note:** Requires `#pragma pack(2)` or similar alignment settings to match the 68k/PPC memory alignment of the original protocol.

### Protocol Debugging Tools

#### Wireshark (Custom Dissectors)

To debug raw hex traffic, Wireshark is indispensable.

- **Filter:** `tcp.port == 5500`
- **Tip:** Look for the `54 52 54 50` (TRTP) signature at the start of the stream to verify the handshake.

#### Hex Editors

Useful for inspecting the [Flat File Objects (FILP)](/binary-structure/) during upload/download debugging.

- **Recommended:** HxD (Windows), Hex Fiend (macOS), or 010 Editor.

### External Resources

- **Preterhuman.net:** Archives of original Hotline documentation and early 1.2x source fragments.
- **HLWiki Icons:** Access the raw icon set directly via [`https://hlwiki.com/icons/Icon_ID.png`](https://hlwiki.com/icons/Icon_ID.png) for use in custom client UI.

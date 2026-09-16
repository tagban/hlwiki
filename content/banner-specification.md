---
title: "BannerSpecification"
nav: "protocol"
categories: ["Hotline", "Technical Specifications"]
mediawiki:
  title: "BannerSpecification"
  revisions: 1
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Banner Specification (Transaction 122)

Banners are the primary visual branding tool in the Hotline protocol. Historically, they appeared at the top of the chat window and served as digital billboards for server identity and cross-server advertising.

### Transaction 122: Get Server Banner

The client sends **Transaction 122** to request the current server banner. The server responds with either the raw binary data or a reference URL.

#### Request Fields

- **Field 101 (Transaction ID):** A unique ID for the transaction.

#### Response Fields

- **Field 152 (Banner Type):** A 4-character constant (OSType) indicating the format.
  - `URL`: The banner is hosted externally.
  - `JPEG`: Raw JFIF/JPEG image data.
  - `GIF`: Raw GIF image data.
- **Field 153 (Banner Data/URL):** The actual content.
  - If Type is `URL`, this is a string (e.g., [`https://hlwiki.com/banners/main.jpg`](https://hlwiki.com/banners/main.jpg)).
  - If Type is `JPEG` or `GIF`, this is the raw binary data.

### Design Guidelines & Spec Tips

To ensure banners render correctly across classic and modern clients, follow these standards:

#### Dimensions

- **Standard Size:** 468 x 60 pixels. This is the optimal size for Hotline 1.23 and 1.5 clients.
- **Wide Format:** 600 x 60 pixels. Supported by 1.8/1.9 and modern community clients.
- **Consistency:** Always maintain a height of **60px** to avoid UI clipping in the chat header.

#### Technical Constraints

- **JPEG Encoding:** Use **Baseline (Standard)** encoding. Older clients (68k/PPC) cannot decode "Progressive" JPEGs.
- **Color Palette:** For GIF banners, use the **Web-Safe (216 color)** palette. This prevents "dithering" on vintage Macintosh systems running in 256-color mode.
- **Compression:** Keep binary banners under 50KB to prevent "Chat Lag" during the initial login handshake.

#### Best Practices

- **External Hosting:** Whenever possible, use the `URL` type. This offloads bandwidth from the Hotline port (5500) to your web server, significantly improving server performance during high traffic.
- **Contrast:** Use high-contrast text and bright borders. Banners are often viewed against the black or charcoal backgrounds of "Dark Mode" skins.

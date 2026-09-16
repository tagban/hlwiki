---
title: "DiscordBridgeSetup"
nav: "protocol"
categories: ["Development"]
mediawiki:
  title: "DiscordBridgeSetup"
  revisions: 1
  last_edited: "2026-03-03"
  contributors: ["Lostarch"]
---

## Setting Up the Discord-Hotline Bridge

This guide explains how to deploy the [Python Bridge Script](/hl-protocol-bridge-script/) on a modern server (Linux, Windows, or macOS).

### Prerequisites

- **Python 3.8+**: Ensure Python is installed on your system.
- **Discord Bot Token**: Required to authenticate with the Discord API.
- **Hotline Server Access**: The IP and Port of the Hotline server you wish to bridge.

### Step 1: Install Dependencies

The bridge relies on two primary libraries: `discord.py` for the bot interface and `aiohttp` for the webhook relay. Open your terminal and run:

``` bash
pip install discord.py aiohttp
```

### Step 2: Create a Discord Webhook

To allow the bot to post messages with custom Hotline icons, you must use a Webhook.

1.  Go to your Discord Server Settings \> Integrations \> Webhooks.
2.  Click "New Webhook."
3.  Copy the **Webhook URL**. You will need this for the config file.

### Step 3: Configure the Bot

Create a file named `config.json` in the same directory as your script. Use the following template:

``` json
{
    "discord_token": "YOUR_BOT_TOKEN_HERE",
    "discord_channel_id": 1234567890123456,
    "discord_webhook_url": "https://discord.com/api/webhooks/...",
    "hotline_host": "72.62.163.172",
    "hotline_port": 5500,
    "manual_admins": ["Tagban", "Knezzen"],
    "admin_prefix": "> **[ADMIN]** ",
    "user_prefix": "```fix\n",
    "user_suffix": "\n```"
}
```

### Step 4: Launching the Bridge

Run the script using the following command:

``` bash
python bridge.py
```

Once connected, you should see `✅ Hotline Connected (Guest Mode)` in your console.

### Troubleshooting

- **Connection Refused**: Ensure the Hotline server allows Guest connections (0x6b).
- **No Messages in Discord**: Verify the `discord_channel_id` is correct and the bot has "View Channel" and "Send Messages" permissions.

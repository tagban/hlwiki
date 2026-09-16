---
title: "DateParameters"
categories: ["Development"]
mediawiki:
  title: "DateParameters"
  revisions: 1
  last_edited: "2024-01-03"
  contributors: ["Lostarch"]
---

Date *parameters* (also referred to as date *objects*) in the Hotline protocol contain the following information. The numerical values, as always, are encoded using Big Endian or Network Byte Order:

Year (2 bytes), Milliseconds (2 bytes), Seconds (4 bytes).

Seconds and milliseconds represent the amount of time that passed since January 1st of any given year. For instance, 70 seconds and 2,000 milliseconds for a year value of 2010 would represent 1 January 2010 at 00:01:12 AM. 432,000 seconds and 5,000 milliseconds for a year value of 2008 would represent 6 January 2008 at 00:00:15 AM (43,210 seconds = 5 days, 10 seconds; 5,000 milliseconds = 5 seconds).

The algorithm for converting to a date parameter is as follows:

| Month     | Seconds ellapsed since 1/1/YYYY 0:00 (at first of month) |
|-----------|----------------------------------------------------------|
| January   | 0                                                        |
| February  | 2,678,400                                                |
| March     | 5,097,600 (non leap year)                                |
| April     | 7,776,000                                                |
| May       | 10,368,000                                               |
| June      | 13,046,400                                               |
| July      | 15,638,400                                               |
| August    | 18,316,800                                               |
| September | 20,995,200                                               |
| October   | 23,587,200                                               |
| November  | 26,265,600                                               |
| December  | 28,857,600                                               |

Given a *SYSTEMTIME*\[<https://msdn.microsoft.com/en-us/library/windows/desktop/ms724950(v=vs.85>).aspx\] structure and an integer array of the above table called *MONTH_SECS*, in order to get the seconds:

`MONTH_SECS[wMonth - 1] +`\
`(if (wMonth > 2) and isLeapYear(wYear): 86400 else 0) +`\
`(wSecond + (60 * (wMinute + 60 * ((wHour + 24 * (wDay - 1))))))`

The year and milliseconds are simply copied over.

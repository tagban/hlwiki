---
title: "AppWarrior"
categories: ["Development", "AppWarrior"]
mediawiki:
  title: "AppWarrior"
  revisions: 6
  last_edited: "2025-04-12"
  contributors: ["Lostarch", "Schala_ghost"]
---

AppWarrior is the toolkit in which the Hotline client/server/tracker suite is built off of. It is heavily undocumented, but if you have [Doxygen](http://doxygen.nl), there's a Doxyfile for AppWarrior's API [here](https://github.com/NebuHiiEjamu/Openline/tree/master/AppWarrior) as well as one for GLoarbLine's additions [here](https://github.com/NebuHiiEjamu/GLoarbLine/tree/master/AppWarrior). As it stands, AppWarrior requires Metrowerks CodeWarrior to build and is configured to target Mac OS Classic PPC, Mac OS X Carbon PPC, and Windows x86, all 32-bit.

A good chunk of AppWarrior's API predates C++'s standard library, standarized in 1998, and as such, much of this API has more efficient alternatives, either from the standard library included with modern C++ compilers, while the GUI and more exotic components have better alternatives with libraries such as [Qt](http://qt.io) or [Boost](http://boost.org), all of which have vastly more modern platform support than AppWarrior.

# Naming Conventions

Identifiers in AppWarrior follow a naming convention. Classes, structs and their member functions are PascalCased, while their variables and typed constants are camelCased. Macros and preprocessor symbols are mostly CAPITALIZED_SNAKE_CASE with a few exceptions. Global functions seem to be lowercased. Enumeration constants seem to be in snake_camelCase style.

| Prefix | Applies to |
|----|----|
| \_ | Internal classes |
| C | Instantiable classes |
| S | Structures |
| T | Function type aliases, as well as classes which are constructed through a modular class |
| U | Modular classes (all static functions) |

## Module overview

| Name | Description |
|----|----|
| ANSI | Various routines for Pascal (byte length-prefixed) string manipulation |
| CPtrList | Implements an intrusively linked list for pointers with a QoL template derivative class |
| GrafTypes | Core geometric types such as Point and Rect for visual programming |
| MoreTypes | Event types such as those for input (keyboard, mouse...) |
| typedefs | Cross-platform primitive type definitions and QoL macros and functions |
| UApplication | General overall application handling |
| UBitString | Dynamically sized bit string manipulation |
| UDateTime | Date and time support |
| UDigest | Hashing for MD5 and UU encoding |
| UError | Error handling |
| UFileSys | File system support |
| UMath | Random number generation, trigonometry, and 64-bit integer operations |
| UMemory | Memory handling |
| UMessageSys | Implements a message bus for multithreaded communication |
| UMouse | Mouse support |
| UOperatingSystem | General OS support |
| URegularTransport | General networking |
| USound | Audio support |
| UText | Text formatting, conversion and evaluation |
| UTimer | Timer support |

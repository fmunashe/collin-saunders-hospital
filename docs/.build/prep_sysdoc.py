#!/usr/bin/env python3
"""Prepend a branded cover to the system documentation and emit a build file."""
import os, datetime

BUILD = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(BUILD, 'HMS_System_Documentation.md')
OUT = os.path.join(BUILD, 'HMS_System_Documentation.prepared.md')
LOGO = os.path.join(BUILD, 'hms-logo.png')

md = open(SRC, encoding='utf-8').read()

# Strip the leading H1/H2 title lines; the cover provides them.
lines = md.splitlines()
# remove first H1 and the following H2 subtitle if present
body_start = 0
for i, ln in enumerate(lines):
    if ln.startswith('## '):
        body_start = i + 1
        break
body = "\n".join(lines[body_start:])

today = datetime.date.today().strftime('%d %B %Y')

cover = f"""---
title: "Hospital Management System (HMS)"
subtitle: "End-to-End System Documentation"
author: "AnalyticsHive — for Collin Saunders Hospital"
date: "{today}"
---

![]({LOGO}){{width=2.6in}}

# Hospital Management System (HMS)

## End-to-End System Documentation

**Client:** Collin Saunders Hospital  
**Prepared by:** AnalyticsHive  
**Version:** 1.0 &nbsp;&nbsp; **Date:** {today}

\\newpage

# Contents
"""

open(OUT, 'w', encoding='utf-8').write(cover + "\n" + body)
print('prepared:', OUT)

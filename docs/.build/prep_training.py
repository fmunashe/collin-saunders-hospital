#!/usr/bin/env python3
"""Prepend a branded cover to the training manual and emit a build file."""
import os, datetime

BUILD = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(BUILD, 'HMS_Training_Manual.md')
OUT = os.path.join(BUILD, 'HMS_Training_Manual.prepared.md')
LOGO = os.path.join(BUILD, 'hms-logo.png')

md = open(SRC, encoding='utf-8').read()

# Drop the leading H1 title; the cover provides it.
lines = md.splitlines()
if lines and lines[0].startswith('# '):
    lines = lines[1:]
body = "\n".join(lines)

today = datetime.date.today().strftime('%d %B %Y')

cover = f"""---
title: "HMS User Training Manual"
author: "AnalyticsHive — for Collin Saunders Hospital"
date: "{today}"
---

![]({LOGO}){{width=2.6in}}

# HMS User Training Manual

**Hospital Management System — Staff Training Guide**  
**Client:** Collin Saunders Hospital  
**Prepared by:** AnalyticsHive  
**Version:** 1.0 &nbsp;&nbsp; **Date:** {today}

\\newpage

# Contents
"""

open(OUT, 'w', encoding='utf-8').write(cover + "\n" + body)
print('prepared:', OUT)

#!/usr/bin/env python3
"""Preprocess the DRMS proposal markdown for a clean branded Word export.

- Prepends a branded cover block (title, prepared-by, client).
- Replaces ```mermaid fenced blocks with a labelled 'Diagram' note so the Word
  document doesn't show raw mermaid source (Word can't render mermaid).
"""
import re, os

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
SRC = os.path.join(ROOT, 'docs', 'DRMS-Proposal.md')
OUT = os.path.join(os.path.dirname(__file__), 'DRMS-Proposal.prepared.md')

md = open(SRC, encoding='utf-8').read()

# Replace mermaid diagram blocks with a clean note (numbered).
counter = {'n': 0}
def repl(m):
    counter['n'] += 1
    return (f"> **Figure {counter['n']}.** *(Architecture / flow diagram — rendered "
            f"in the interactive documentation; omitted here for print clarity.)*")
md = re.sub(r"```mermaid.*?```", repl, md, flags=re.DOTALL)

# The very first H1 + H2 act as the title; strip them, we build our own cover.
md = re.sub(r"^# .*?\n## Solution Proposal\n", "", md, count=1, flags=re.DOTALL)

logo = os.path.join(os.path.dirname(__file__), 'hms-logo.png')

cover = f"""---
title: "Enterprise Document & Records Management System (DRMS)"
subtitle: "Solution Proposal"
author: "Prepared by AnalyticsHive for Hulett Tongaat Zimbabwe"
date: "{__import__('datetime').date.today().strftime('%d %B %Y')}"
---

![]({logo}){{width=2.6in}}

# Enterprise Document & Records Management System (DRMS)

## Solution Proposal

**Prepared for:** Hulett Tongaat Zimbabwe — Senior Management, IT Leadership & Procurement

**Prepared by:** AnalyticsHive — Solution Architecture Team

**Document status:** Proposal for approval of a discovery & implementation project

\\newpage

"""

open(OUT, 'w', encoding='utf-8').write(cover + md)
print('prepared markdown written:', OUT, '| diagrams replaced:', counter['n'])

#!/usr/bin/env python3
"""Apply HMS brand colours to the pandoc reference.pptx theme."""
import os, re

BUILD = os.path.dirname(os.path.abspath(__file__))
THEME = os.path.join(BUILD, 'refpptx', 'ppt', 'theme', 'theme1.xml')

PRIMARY = '006838'       # HMS green
PRIMARY_DARK = '00542D'  # dark green
ACCENT = 'FDB913'        # gold

s = open(THEME, encoding='utf-8').read()

# Recolour the default Office scheme colours to HMS brand.
replacements = {
    '1F497D': PRIMARY_DARK,  # dark1/text2 (headings)
    '4F81BD': PRIMARY,       # accent1 (titles, bars)
    '9BBB59': ACCENT,        # accent3 -> gold
    'F79646': ACCENT,        # accent6 -> gold
}
for old, new in replacements.items():
    s = s.replace(f'val="{old}"', f'val="{new}"')

open(THEME, 'w', encoding='utf-8').write(s)
print('pptx theme branded.')

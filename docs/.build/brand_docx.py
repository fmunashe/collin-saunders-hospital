#!/usr/bin/env python3
"""Apply HMS brand styling to the pandoc reference.docx styles.xml."""
import re, os

BUILD = os.path.dirname(os.path.abspath(__file__))
src = os.path.join(BUILD, 'refdocx', 'word', 'styles.xml')
s = open(src, encoding='utf-8').read()

PRIMARY = '006838'       # HMS green
PRIMARY_DARK = '00542D'  # dark green

# Map every "blue" heading/title colour used by the default reference to HMS green.
replacements = {
    '0F4761': PRIMARY,      # main heading/title colour (Word default)
    '4F81BD': PRIMARY,      # legacy heading colour
    '365F91': PRIMARY_DARK, # legacy heading shade
}
for old, new in replacements.items():
    s = s.replace(f'w:val="{old}"', f'w:val="{new}"')

# Drop themeColor hints so our explicit colour wins in all viewers.
s = re.sub(r'\s+w:themeColor="accent1"(?:\s+w:themeShade="[0-9A-Fa-f]{2}")?', '', s)

open(src, 'w', encoding='utf-8').write(s)
print('styles.xml branded: headings -> #%s' % PRIMARY)

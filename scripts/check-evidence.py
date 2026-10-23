#!/usr/bin/env python3
"""Validate BNG Evidence RU/EN client content and editable ACF schema."""
from pathlib import Path
import json
import re

root = Path(__file__).resolve().parents[1]
source = json.loads((root / 'data/source-content.json').read_text(encoding='utf-8'))['copy']
template = (root / 'template-parts/original/evidence.html').read_text(encoding='utf-8')
required = ('title','sub','nowTitle','nowNote','provable','earnTitle','earnNote','earned','dossierTitle','dossierBody','dossierCta')
for locale in ('ru','en'):
    copy = source[locale]['evidence']
    group = json.loads((root / f'acf-json/group_bng_evidence_{locale}.json').read_text(encoding='utf-8'))
    by_name = {item.get('name'): item for item in group['fields'] if item.get('name')}
    for field in required:
        field_name = f'bng_{locale}_evidence_{field}'
        if not copy.get(field) or field_name not in by_name:
            raise SystemExit(f'Missing {locale} evidence content/ACF field: {field}')
        if isinstance(copy[field], str) and by_name[field_name].get('default_value') != copy[field]:
            raise SystemExit(f'Changed {locale} original client wording: {field}')
    for key, width in (('provable',1),('earned',2)):
        rows = copy[key]
        if not rows:
            raise SystemExit(f'Empty {locale}.{key}')
        if any((len(row) if isinstance(row,list) else 1) != width for row in rows):
            raise SystemExit(f'Unexpected {locale}.{key} row layout')
        subfields = by_name[f'bng_{locale}_evidence_{key}'].get('sub_fields',[])
        if len(subfields) != width:
            raise SystemExit(f'Incorrect editable subfield count: {locale}.{key}')
    print(f'{locale.upper()}: {len(copy["provable"])} verified facts and {len(copy["earned"])} pending claims, all text editable')
for token in ('t.evidence.title','t.evidence.sub','t.evidence.nowTitle',
              't.evidence.nowNote','provable','t.evidence.earnTitle',
              't.evidence.earnNote','earned','t.evidence.dossierTitle',
              't.evidence.dossierBody','t.evidence.dossierCta'):
    if '{{ ' + token + ' }}' not in template:
        raise SystemExit('Missing client template binding: ' + token)
print('Approved client layout and dynamic text bindings: OK')

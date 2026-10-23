#!/usr/bin/env python3
"""News and Company: bilingual source completeness and editable ACF schema."""
from pathlib import Path
import json
root=Path(__file__).resolve().parents[1]
source=json.loads((root/'data/source-content.json').read_text(encoding='utf-8'))['copy']
for section, required in {
 'news':('title','sub','logTitle','logNote','items','aheadTitle','aheadNote','ahead','mediaTitle','mediaBody','mediaCta'),
 'company':('title','sub','proof','pillars','archTitle','arch','aboutTitle','about','contactTitle','contacts')
}.items():
 markup=(root/f'template-parts/original/{section}.html').read_text(encoding='utf-8')
 for locale in ('ru','en'):
  group=json.loads((root/f'acf-json/group_bng_{section}_{locale}.json').read_text(encoding='utf-8'))
  fields={f.get('name'):f for f in group['fields'] if f.get('name')}
  part=source[locale][section]
  for key in required:
   assert part.get(key), f'Missing source {locale}.{section}.{key}'
   field=fields.get(f'bng_{locale}_{section}_{key}')
   assert field, f'Missing ACF {locale}.{section}.{key}'
   if isinstance(part[key],str):
    assert field.get('default_value')==part[key], f'Changed client text {locale}.{section}.{key}'
   else:
    assert field['type']=='repeater', f'{locale}.{section}.{key} is not editable repeater'
    for row in part[key]:
     assert isinstance(row,(list,str)) and row, f'Blank record {locale}.{section}.{key}'
  if section=='news':
   assert len(part['items'])==8 and len(part['ahead'])==3
   assert all(len(row)==4 for row in part['items'])
   assert len(fields[f'bng_{locale}_news_ahead']['sub_fields'])==3
  else:
   assert len(part['pillars'])==4 and len(part['arch'])==3 and len(part['contacts'])==4
   assert len(part['about'])==5
  print(f'{locale.upper()} {section}: source and all ACF fields present')
 if section=='news':
  bindings=('t.news.title','t.news.sub','t.news.mediaTitle','t.news.mediaBody','t.news.mediaCta','t.news.logTitle','t.news.logNote','newsItems','t.news.aheadTitle','t.news.aheadNote','newsAhead')
 else:
  bindings=('t.company.title','t.company.sub','t.company.proof','t.company.aboutTitle','t.company.archTitle','t.company.contactTitle','pillars','archCells','aboutParas','contacts')
 for token in bindings:
  assert '{{ '+token+' }}' in markup, f'Missing displayed text {section}: {token}'
print('RU/EN News and Company: all client text mapped to editable fields and markup')

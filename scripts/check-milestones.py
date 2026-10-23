#!/usr/bin/env python3
"""Check that RU/EN milestones preserve source text and remain editable in ACF."""
from pathlib import Path
import json

root = Path(__file__).resolve().parents[1]
source = json.loads((root / 'data/source-content.json').read_text(encoding='utf-8'))['copy']
markup = (root / 'template-parts/original/milestones.html').read_text(encoding='utf-8')
client = (root / 'assets/original/page.html').read_text(encoding='utf-8')
acf = (root / 'inc/acf.php').read_text(encoding='utf-8')
for locale in ('ru', 'en'):
    copy = source[locale]['milestones']
    group = json.loads((root / f'acf-json/group_bng_milestones_{locale}.json').read_text(encoding='utf-8'))
    fields = {f.get('name'): f for f in group['fields'] if f.get('name')}
    for name in ('title', 'sub', 'doneTitle', 'aheadTitle', 'done', 'ahead'):
        assert name in copy and copy[name], f'Missing source {locale}.{name}'
        field = fields.get(f'bng_{locale}_milestones_{name}')
        assert field, f'Missing ACF field {locale}.{name}'
        if isinstance(copy[name], str):
            assert field.get('default_value') == copy[name], f'Source text changed: {locale}.{name}'
        else:
            assert field['type'] == 'repeater', f'{locale}.{name} should be repeater'
            assert [f['name'] for f in field['sub_fields']] == ['c0','c1','c2']
            for row in copy[name]:
                assert len(row) == 3 and all(isinstance(value,str) and value for value in row), row
    assert len(copy['done']) == 9, f'Unexpected completed milestone count {locale}'
    assert len(copy['ahead']) == 8, f'Unexpected upcoming milestone count {locale}'
    print(f'{locale.upper()}: 9 completed + 8 future milestones, dates/titles/descriptions editable')
for token in ('t.milestones.title','t.milestones.sub','t.milestones.doneTitle',
              't.milestones.aheadTitle','snakeRows','r.cells','m.when','m.head','m.body'):
    assert '{{ ' + token + ' }}' in markup, f'Missing original timeline binding {token}'
assert 't.milestones.done.map(' in client and 't.milestones.ahead.map(' in client
assert '_bng_milestones_seeded_' in acf and "bng_section_id('milestones')" in acf
print('Original client snake timeline + ACF source: OK')

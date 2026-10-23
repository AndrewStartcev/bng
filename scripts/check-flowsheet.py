#!/usr/bin/env python3
"""Regression checks for the two BNG process flowsheets and ACF source bindings."""
from pathlib import Path
import json

root = Path(__file__).resolve().parents[1]
copy = json.loads((root / 'data/source-content.json').read_text(encoding='utf-8'))['copy']
streams = json.loads((root / 'data/flowsheet-streams.json').read_text(encoding='utf-8'))
template = (root / 'template-parts/original/flowsheet.html').read_text(encoding='utf-8')
frontend = (root / 'assets/original/page.html').read_text(encoding='utf-8')
bridge = (root / 'front-page.php').read_text(encoding='utf-8')

fields = ('title','sub','pick','feedKicker','feed','feedNote','all','routesTitle',
          'routesRec','routesSelected','routes','routesNote','givesTitle',
          'costsTitle','gives','costs')
for lang in ('ru','en'):
    source = copy[lang]['flow']
    group = json.loads((root / f'acf-json/group_bng_flowsheet_{lang}.json').read_text(encoding='utf-8'))
    acf = {f.get('name'): f for f in group['fields'] if f.get('name')}
    for field in fields:
        assert field in source and source[field], f'Missing original {lang}.flow.{field}'
        name = f'bng_{lang}_flowsheet_{field}'
        assert name in acf, f'Missing ACF field {name}'
        if isinstance(source[field], str):
            assert acf[name].get('default_value') == source[field], f'Changed source text {name}'
        else:
            assert acf[name].get('type') == 'repeater', name
    assert len(source['routes']) == 3, f'Unexpected original route count ({lang})'
    assert len(source['gives']) == 3 and len(source['costs']) == 3
    assert len(streams[lang]) == 4, f'Expected four {lang} element streams'
    streamacf = acf[f'bng_{lang}_flowsheet_streams']
    assert streamacf['type'] == 'repeater'
    sub = {f['name']: f for f in streamacf['sub_fields']}
    for name in ('symbol','atomic','name','count','buyers','products'):
        assert name in sub, f'Missing {name} for {lang} streams'
    assert sub['products']['type'] == 'repeater'
    for stream in streams[lang]:
        assert all(stream.get(k) for k in ('symbol','atomic','name','count','buyers','products')), stream
        for product in stream['products']:
            assert product.get('formula') and product.get('name'), product
    print(f'{lang.upper()}: full flowsheet, 3 process routes, 4 product streams editable')
for key in ('t.flow.title','t.flow.sub','t.flow.pick','t.flow.feedKicker','t.flow.feed',
            't.flow.feedNote','t.flow.all','t.flow.routesTitle','flowRoutes',
            't.flow.routesSelected','t.flow.givesTitle','flowGives',
            't.flow.costsTitle','flowCosts','oreRows','streams','streamTabs'):
    assert '{{ ' + key + ' }}' in template, f'Missing frontend flow data binding: {key}'
assert 'window.BNG_FLOWSHEET_STREAMS' in frontend and 'window.BNG_FLOWSHEET_STREAMS' in bridge
assert 'bng_\' . $locale . \'_flowsheet_streams' in bridge
print('Flowsheet source and frontend bindings: OK')

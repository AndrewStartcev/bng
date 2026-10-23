#!/usr/bin/env python3
"""Validate full RU/EN products catalog and editable nested ACF structure."""
from pathlib import Path
import json

root = Path(__file__).resolve().parents[1]
source = json.loads((root / 'data/source-content.json').read_text(encoding='utf-8'))
products, grades = source['products'], source['grades']
assert len(products) == 10, 'Client approved ten products'
grade_count = sum(len(grades.get(item['code'], [])) for item in products)
parameter_count = sum(len(g.get('params', [])) for item in products for g in grades.get(item['code'], []))
assert grade_count >= 10 and parameter_count >= 100
page = (root / 'assets/original/page.html').read_text(encoding='utf-8')
php = (root / 'front-page.php').read_text(encoding='utf-8')
template = (root / 'template-parts/original/products.html').read_text(encoding='utf-8')
assert 'window.BNG_PRODUCTS_DATA' in page and 'window.BNG_PRODUCTS_DATA' in php
assert 'const visible = editableCatalog.filter(' in page
assert 'grades.map((g) =>' in page
assert 'g.params.map' in page
assert 'slice(0, 4)' not in page[page.index('const editableCatalog'):page.index('const active = this.state.stream')]
assert 'bng-products-table-wrap' in template
for locale in ('ru','en'):
    group = json.loads((root / f'acf-json/group_bng_products_{locale}.json').read_text(encoding='utf-8'))
    fields = {x.get('name'): x for x in group['fields'] if x.get('name')}
    for key, val in source['copy'][locale]['products'].items():
        field = fields.get(f'bng_{locale}_products_{key}')
        assert field, f'Missing ACF {locale}.{key}'
        if isinstance(val, str):
            assert field.get('default_value') == val, f'Changed original text {locale}.{key}'
    catalog = fields[f'bng_{locale}_products_catalog']
    assert catalog['type'] == 'repeater'
    rootfields = {x['name']: x for x in catalog['sub_fields']}
    for key in ('code','name','formula','segment','stream','buyer','status','grades'):
        assert key in rootfields, f'Missing product field {locale}.{key}'
    gr = {x['name']: x for x in rootfields['grades']['sub_fields']}
    for key in ('code','name','source_file','attachment','params'):
        assert key in gr, f'Missing grade field {locale}.{key}'
    assert {x['name'] for x in gr['params']['sub_fields']} == {'label','value','unit'}
    for product in products:
        assert product[locale]['name'] and product[locale]['buyer'] and product[locale]['status']
        for grade in grades.get(product['code'], []):
            assert grade[locale]['name'] and grade['code']
            for param in grade['params']:
                assert all(k in param[locale] for k in ('k','v','u'))
    print(f'{locale.upper()}: 10 products, {grade_count} grades, {parameter_count} parameters editable')
print('Client originals, ACF schema and frontend bindings OK')

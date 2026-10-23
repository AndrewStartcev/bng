<?php
/**
 * Section-specific ACF meta boxes, based on the RU/EN source content.
 * No global page builder: fields correspond to existing client blocks.
 */
defined('ABSPATH') || exit;
add_filter('acf/settings/save_json', static fn() => get_template_directory() . '/acf-json');
add_filter('acf/settings/load_json', static function (array $paths): array {
    $paths[] = get_template_directory() . '/acf-json';
    return array_unique($paths);
});
function bng_editor_layout(): array { return ['home'=>['Первый экран'=>['kicker','title','sub','cta1','cta2'],'Видео'=>['figcap'],'Позиционирование'=>['posTitle','posBody','cells']],'products'=>['Первый экран'=>['title','sub'],'Статус продукции'=>['statusLabel','statusBody'],'Таблица'=>['filter','colProduct','colFormula','colSegment','colBuyer','colSheet','colStatus','colGrades','download','countLabel','gradeFoot'],'Направления'=>['cards']],'rock'=>['Первый экран'=>['title','sub','facts'],'Состав руды'=>['compTitle','compNote'],'Преимущества'=>['surfaceTitle','points'],'Месторождение'=>['mapTitle','mapSub']],'flowsheet'=>['Первый экран'=>['title','sub'],'Исходное сырьё'=>['pick','feedKicker','feed','feedNote','all'],'Технологии'=>['routesTitle','routesRec','routesSelected','routes','routesNote'],'Результаты'=>['givesTitle','gives','costsTitle','costs']],'evidence'=>['Первый экран'=>['title','sub'],'Подтверждено'=>['nowTitle','nowNote','provable'],'Предстоит доказать'=>['earnTitle','earnNote','earned'],'Техническое досье'=>['dossierTitle','dossierBody','dossierCta']],'milestones'=>['Первый экран'=>['title','sub'],'Выполнено'=>['doneTitle','done'],'Впереди'=>['aheadTitle','ahead']],'news'=>['Первый экран'=>['title','sub'],'Публикации'=>['logTitle','logNote','items'],'В планах'=>['aheadTitle','aheadNote','ahead'],'Контакты для прессы'=>['mediaTitle','mediaBody','mediaCta']],'company'=>['Первый экран'=>['title','sub','proof','pillars'],'Структура бренда'=>['archTitle','arch'],'О компании'=>['aboutTitle','about'],'Контакты'=>['contactTitle','contacts']]]; }
function bng_editor_labels(): array { return ['title'=>'Заголовок H1','sub'=>'Описание','kicker'=>'Надпись над заголовком','cta1'=>'Первая кнопка','cta2'=>'Вторая кнопка','figcap'=>'Подпись к видео','posTitle'=>'Заголовок блока','posBody'=>'Описание блока','cells'=>'Карточки','facts'=>'Факты и цифры','points'=>'Преимущества','compTitle'=>'Заголовок состава','compNote'=>'Пояснение к составу','mapTitle'=>'Заголовок карты','mapSub'=>'Описание месторождения','statusLabel'=>'Короткий статус','statusBody'=>'Описание статуса','filter'=>'Название фильтра','download'=>'Надпись на кнопке PDF','gradeFoot'=>'Примечание под таблицей','countLabel'=>'Подпись счётчика','cards'=>'Карточки сегментов','routes'=>'Технологические схемы','gives'=>'Преимущества процесса','costs'=>'Ограничения','done'=>'Завершённые этапы','ahead'=>'Будущие этапы','items'=>'Публикации','earned'=>'Требуют подтверждения','provable'=>'Подтверждённые факты','arch'=>'Архитектура бренда','about'=>'Абзацы описания','contacts'=>'Контактные данные','pillars'=>'Принципы компании']; }

function bng_editor_fields(string $section, string $lang): array {
    $source = bng_source()['copy'][$lang][$section === 'flowsheet' ? 'flow' : $section] ?? [];
    $layouts = bng_editor_layout()[$section] ?? [];
    $labels = bng_editor_labels();
    $fields = [];
    foreach ($layouts as $tab => $keys) {
        $fields[] = [
            'key' => 'field_bng_tab_' . $section . '_' . $lang . '_' . count($fields),
            'type' => 'tab', 'label' => $tab, 'placement' => 'left',
        ];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $source)) continue;
            $value = $source[$key];
            $field_name = 'bng_' . $lang . '_' . $section . '_' . $key;
            $label = $labels[$key] ?? ucfirst((string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $key));
            if (is_array($value)) {
                $first = $value[0] ?? '';
                $sample = is_array($first) ? array_values($first) : [$first];
                $subfields = [];
                foreach ($sample as $n => $item) {
                    $text = (string) $item;
                    $type = (function_exists('mb_strlen') ? mb_strlen($text) : strlen($text)) > 85 ? 'textarea' : 'text';
                    $subfields[] = [
                        'key' => 'field_bng_' . $lang . '_' . $section . '_' . $key . '_' . $n,
                        'name' => 'c' . $n,
                        'label' => count($sample) === 1 ? 'Текст' : ['Дата / номер', 'Заголовок', 'Описание', 'Примечание'][$n] ?? ('Поле ' . ($n + 1)),
                        'type' => $type,
                        'rows' => $type === 'textarea' ? 3 : 0,
                        'wrapper' => ['width' => count($sample) === 1 ? '100' : ($n === 0 ? '25' : ($n === 1 ? '35' : '40'))],
                    ];
                }
                $fields[] = [
                    'key' => 'field_bng_' . $lang . '_' . $section . '_' . $key,
                    'name' => $field_name, 'label' => $label,
                    'type' => 'repeater', 'layout' => count($sample) > 2 ? 'block' : 'row',
                    'button_label' => 'Добавить строку', 'sub_fields' => $subfields,
                ];
            } else {
                $long = (function_exists('mb_strlen') ? mb_strlen((string) $value) : strlen((string) $value)) > 140;
                $fields[] = [
                    'key' => 'field_bng_' . $lang . '_' . $section . '_' . $key,
                    'name' => $field_name, 'label' => $label,
                    'type' => $long ? 'textarea' : 'text',
                    'rows' => $long ? 4 : 0,
                    'default_value' => (string) $value,
                    'wrapper' => ['width' => $long ? '100' : '50'],
                ];
            }
        }
        if ($section === 'home' && $tab === 'Видео') {
            $fields[] = [
                'key'=>'field_bng_' . $lang . '_home_video','name'=>'bng_home_video_' . $lang,
                'label'=>'Видеофайл','type'=>'file','return_format'=>'id',
                'mime_types'=>'mp4,webm','wrapper'=>['width'=>'50'],
            ];
            $fields[] = [
                'key'=>'field_bng_' . $lang . '_home_poster','name'=>'bng_home_poster_' . $lang,
                'label'=>'Постер видео','type'=>'image','return_format'=>'id',
                'wrapper'=>['width'=>'50'],
            ];
        }
        if ($section === 'rock' && $tab === 'Месторождение') {
            $fields[] = [
                'key'=>'field_bng_' . $lang . '_gallery','name'=>'bng_gallery_' . $lang,
                'label'=>'Фотографии (порядок слева направо)','type'=>'gallery',
                'return_format'=>'id','preview_size'=>'medium',
            ];
        }
        if ($section === 'products' && $tab === 'Таблица') {
            $fields[] = [
                'key'=>'field_bng_' . $lang . '_specs','name'=>'bng_specifications_' . $lang,
                'label'=>'PDF по коду марки','type'=>'repeater','layout'=>'row',
                'button_label'=>'Добавить спецификацию','sub_fields'=>[
                    ['key'=>'field_bng_' . $lang . '_spec_code','name'=>'code',
                     'label'=>'Код марки','type'=>'text','required'=>1,'wrapper'=>['width'=>'35']],
                    ['key'=>'field_bng_' . $lang . '_spec_file','name'=>'file',
                     'label'=>'PDF','type'=>'file','return_format'=>'id','mime_types'=>'pdf',
                     'required'=>1,'wrapper'=>['width'=>'65']],
                ],
            ];
        }
    }
    if ($section !== 'home') {
        $fields[] = ['key'=>'field_bng_tab_' . $section . '_' . $lang . '_extra',
                     'label'=>'Дополнительно','type'=>'tab','placement'=>'left'];
        $fields[] = ['key'=>'field_bng_' . $lang . '_body','label'=>'Текст под блоками',
                     'name'=>'bng_body_' . $lang,'type'=>'wysiwyg','toolbar'=>'basic',
                     'tabs'=>'visual','media_upload'=>1];
        $fields[] = ['key'=>'field_bng_' . $lang . '_docs','label'=>'Дополнительные PDF',
                     'name'=>'bng_documents_' . $lang,'type'=>'repeater','layout'=>'row',
                     'button_label'=>'Добавить PDF','sub_fields'=>[
                        ['key'=>'field_bng_' . $lang . '_doc_title','label'=>'Название','name'=>'title',
                         'type'=>'text','wrapper'=>['width'=>'50']],
                        ['key'=>'field_bng_' . $lang . '_doc_file','label'=>'Файл','name'=>'file',
                         'type'=>'file','return_format'=>'id','mime_types'=>'pdf',
                         'wrapper'=>['width'=>'50']]
                     ]];
    }
    return $fields;
}

add_action('acf/init', static function (): void {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title'=>'Общие настройки сайта','menu_title'=>'Сайт',
            'menu_slug'=>'bng-settings','redirect'=>false,
            'capability'=>'manage_options','position'=>3,
            'icon_url'=>'dashicons-admin-site-alt3'
        ]);
    }

});


/**
 * Portable location matcher for ACF Local JSON.
 * Section fields belong to the existing canonical CPT record, including legacy -ru slugs.
 * The rule never relies on deployment-specific numeric post IDs stored in JSON.
 */
add_filter('acf/location/rule_types', static function (array $choices): array {
    $choices['BNG']['bng_section'] = 'Раздел BNG';
    return $choices;
});
add_filter('acf/location/rule_values/bng_section', static function (array $choices): array {
    foreach (array_slice(bng_sections(), 1) as $section) {
        $labels = bng_labels('ru');
        $choices[$section] = $labels[$section] ?? $section;
    }
    return $choices;
});
add_filter('acf/location/rule_match/bng_section', static function ($match, array $rule, array $options): bool {
    $section = (string) ($rule['value'] ?? '');
    if (!in_array($section, array_slice(bng_sections(), 1), true)) {
        return false;
    }
    $raw_post_id = (string) ($options['post_id'] ?? '');
    if (!preg_match('/^(?:post_)?([0-9]+)$/', $raw_post_id, $id_match)) {
        return false;
    }
    $post_id = (int) $id_match[1];
    $is_match = $post_id > 0 && get_post_type($post_id) === 'bng_section'
        && $post_id === bng_section_id($section);
    return ($rule['operator'] ?? '==') === '!=' ? !$is_match : $is_match;
}, 10, 3);

/** Preload original rows into an empty repeater without overwriting saved data. */
add_filter('acf/load_value',static function ($value,$post_id,$field) {
    if (($field['type'] ?? '') !== 'repeater' || ($value !== null && $value !== false)) {
        return $value;
    }
    if (!preg_match('/^bng_(ru|en)_(home|products|rock|flowsheet|evidence|milestones|news|company)_([A-Za-z0-9]+)$/', (string)($field['name'] ?? ''), $match)) {
        return $value;
    }
    if (!is_numeric($post_id) || metadata_exists('post',(int)$post_id,$field['name'])) {
        return $value;
    }
    $source=bng_source()['copy'][$match[1]][$match[2]==='flowsheet'?'flow':$match[2]][$match[3]] ?? null;
    if (!is_array($source)) return $value;
    $rows=[];
    foreach ($source as $row) {
        $cells=is_array($row)?array_values($row):[$row];
        $mapped=[];
        foreach ($cells as $index=>$cell) $mapped['c'.$index]=$cell;
        $rows[]=$mapped;
    }
    return $rows;
},10,3);

/**
 * Repair imported home repeaters that had a saved row count but no row values.
 * Run during an admin visit, after the managed front page has been created.
 * Never overwrite a card that contains an editor-supplied value.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = (int) get_option('page_on_front');
    if (!$post_id || get_post_type($post_id) !== 'page') return;
    foreach (['ru', 'en'] as $locale) {
        $name = 'bng_' . $locale . '_home_cells';
        $original = bng_source()['copy'][$locale]['home']['cells'] ?? [];
        if (!is_array($original) || !$original) continue;

        // Count-only legacy data can exist after the repeater was first saved:
        // row count exists, but individual c0/c1 fields have never been populated.
        $has_content = false;
        $raw_count = (int) get_post_meta($post_id, $name, true);
        foreach (range(0, max(count($original), $raw_count) - 1) as $row) {
            foreach (['c0', 'c1'] as $column) {
                $stored = get_post_meta($post_id, $name . '_' . $row . '_' . $column, true);
                if (is_scalar($stored) && trim((string) $stored) !== '') {
                    $has_content = true;
                    break 2;
                }
            }
        }
        if ($has_content) continue;

        $rows = [];
        foreach ($original as $entry) {
            $cells = is_array($entry) ? array_values($entry) : [(string) $entry];
            $rows[] = [
                'field_bng_' . $locale . '_home_cells_0' => (string) ($cells[0] ?? ''),
                'field_bng_' . $locale . '_home_cells_1' => (string) ($cells[1] ?? ''),
            ];
        }
        // ACF writes both the subfield values and their reference metadata.
        update_field('field_bng_' . $locale . '_home_cells', $rows, $post_id);
    }
}, 30);

/**
 * Seed the approved client copy for the Rock section only.
 * Runs when an editor opens wp-admin after the canonical CPT record exists.
 * Existing saved text (including intentionally empty strings) is never overwritten.
 * Repeaters whose row counters were saved but whose cells are all blank are repaired.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('rock');
    if (!$post_id) return;
    foreach (['ru', 'en'] as $lang) {
        $source = bng_source()['copy'][$lang]['rock'] ?? [];
        if (!is_array($source)) continue;
        foreach ($source as $name => $value) {
            $meta = 'bng_' . $lang . '_rock_' . $name;
            $key = 'field_bng_' . $lang . '_rock_' . $name;
            if (!is_array($value)) {
                if (!metadata_exists('post', $post_id, $meta)) {
                    update_field($key, (string) $value, $post_id);
                }
                continue;
            }
            $row_count = (int) get_post_meta($post_id, $meta, true);
            $nonempty = false;
            foreach (range(0, max(count($value), $row_count) - 1) as $i) {
                foreach (['c0', 'c1'] as $col) {
                    $cell = get_post_meta($post_id, $meta . '_' . $i . '_' . $col, true);
                    if (is_scalar($cell) && trim((string) $cell) !== '') {
                        $nonempty = true;
                        break 2;
                    }
                }
            }
            if ($nonempty) continue;
            $rows = [];
            foreach ($value as $item) {
                $parts = is_array($item) ? array_values($item) : [(string) $item];
                $rows[] = [
                    $key . '_0' => (string) ($parts[0] ?? ''),
                    $key . '_1' => (string) ($parts[1] ?? ''),
                ];
            }
            update_field($key, $rows, $post_id);
        }
    }
}, 40);

/**
 * Fill the six approved client map cards (and source note) in the canonical
 * "Rock" CPT once. Respect existing edited ACF rows; repair count-only,
 * entirely blank repeaters left by earlier imports.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('rock');
    if (!$post_id) return;
    $path = get_template_directory() . '/data/rock-map-content.json';
    $defaults = is_readable($path) ? json_decode((string) file_get_contents($path), true) : [];
    if (!is_array($defaults)) return;
    foreach (['ru','en'] as $lang) {
        $source = $defaults[$lang] ?? [];
        if (empty($source['cards']) || !is_array($source['cards'])) continue;
        $key = 'field_bng_' . $lang . '_rock_map_cards';
        $name = 'bng_' . $lang . '_rock_map_cards';
        $count = (int) get_post_meta($post_id, $name, true);
        $filled = false;
        for ($i = 0; $i < max($count, count($source['cards'])); $i++) {
            foreach (['label','title','distance','note'] as $subfield) {
                $saved = get_post_meta($post_id, $name . '_' . $i . '_' . $subfield, true);
                if (is_scalar($saved) && trim((string) $saved) !== '') {
                    $filled = true;
                    break 2;
                }
            }
        }
        if (!$filled) {
            $rows = [];
            foreach ($source['cards'] as $cells) {
                $rows[] = [
                    'field_bng_' . $lang . '_rock_map_card_label' => (string) ($cells[0] ?? ''),
                    'field_bng_' . $lang . '_rock_map_card_title' => (string) ($cells[1] ?? ''),
                    'field_bng_' . $lang . '_rock_map_card_distance' => (string) ($cells[2] ?? ''),
                    'field_bng_' . $lang . '_rock_map_card_note' => (string) ($cells[3] ?? ''),
                ];
            }
            update_field($key, $rows, $post_id);
        }
        $footnote = 'bng_' . $lang . '_rock_map_footnote';
        if (!metadata_exists('post', $post_id, $footnote)) {
            update_field('field_bng_' . $lang . '_rock_map_footnote', (string) ($source['footnote'] ?? ''), $post_id);
        }
    }
}, 50);

/**
 * Seed ore composition values from the approved client source.
 * Only an uninitialized or entirely blank legacy repeater is populated.
 * Once any row has been edited it remains authoritative.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('rock');
    if (!$post_id) return;
    $path = get_template_directory() . '/data/rock-ore-content.json';
    $source = is_readable($path) ? json_decode((string) file_get_contents($path), true) : null;
    if (!is_array($source)) return;
    foreach (['ru','en'] as $lang) {
        $name = 'bng_' . $lang . '_rock_ore';
        $field_key = 'field_bng_' . $lang . '_rock_ore';
        $original = $source[$lang] ?? null;
        if (!is_array($original) || !$original) continue;
        $count = (int) get_post_meta($post_id, $name, true);
        $has_value = false;
        for ($i = 0; $i < max($count, count($original)); $i++) {
            foreach (['formula','name','percentage','display'] as $part) {
                $value = get_post_meta($post_id, $name . '_' . $i . '_' . $part, true);
                if (is_scalar($value) && trim((string) $value) !== '') {
                    $has_value = true;
                    break 2;
                }
            }
        }
        if ($has_value) continue;
        $rows = [];
        foreach ($original as $entry) {
            $rows[] = [
                'field_bng_' . $lang . '_rock_ore_formula' => (string) ($entry['formula'] ?? ''),
                'field_bng_' . $lang . '_rock_ore_name' => (string) ($entry['name'] ?? ''),
                'field_bng_' . $lang . '_rock_ore_percentage' => (float) ($entry['percentage'] ?? 0),
                'field_bng_' . $lang . '_rock_ore_display' => (string) ($entry['display'] ?? ''),
            ];
        }
        update_field($field_key, $rows, $post_id);
    }
}, 60);

/**
 * Evidence RU/EN — one-time ACF import of every client-supplied text and row.
 * Preserve existing editorial values. Empty legacy repeaters are repaired only
 * if no editor values exist; a marker prevents refilling deliberate deletions.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('evidence');
    if (!$post_id) return;
    $source = bng_source()['copy'] ?? [];
    foreach (['ru', 'en'] as $locale) {
        $values = $source[$locale]['evidence'] ?? [];
        if (!is_array($values)) continue;
        foreach ($values as $slug => $default) {
            $name = 'bng_' . $locale . '_evidence_' . $slug;
            $key = 'field_bng_' . $locale . '_evidence_' . $slug;
            if (!is_array($default)) {
                if (!metadata_exists('post', $post_id, $name)) {
                    update_field($key, (string) $default, $post_id);
                }
                continue;
            }
            $marker = '_bng_evidence_seeded_' . $locale . '_' . $slug;
            if (get_post_meta($post_id, $marker, true)) continue;
            $row_count = (int) get_post_meta($post_id, $name, true);
            $has_content = false;
            for ($i = 0; $i < max(count($default), $row_count); $i++) {
                foreach (['c0', 'c1'] as $col) {
                    $saved = get_post_meta($post_id, $name . '_' . $i . '_' . $col, true);
                    if (is_scalar($saved) && trim((string) $saved) !== '') {
                        $has_content = true;
                        break 2;
                    }
                }
            }
            if ($has_content) {
                update_post_meta($post_id, $marker, '1');
                continue;
            }
            $rows = [];
            foreach ($default as $item) {
                $cells = is_array($item) ? array_values($item) : [(string) $item];
                $row = [$key . '_0' => (string) ($cells[0] ?? '')];
                if ($slug === 'earned') $row[$key . '_1'] = (string) ($cells[1] ?? '');
                $rows[] = $row;
            }
            if (update_field($key, $rows, $post_id)) {
                update_post_meta($post_id, $marker, '1');
            }
        }
    }
}, 70);

/**
 * Flowsheet: seed all original RU/EN fields, routes, benefits and constraints,
 * as well as the four nested ACF stream/product lists.
 * Never overwrite existing editorial content. Mark seeded repeaters so
 * a deliberate deletion in the editor does not trigger re-import.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('flowsheet');
    if (!$post_id) return;
    $defaults = bng_source()['copy'] ?? [];
    $path = get_template_directory() . '/data/flowsheet-streams.json';
    $stream_defaults = is_readable($path) ? json_decode((string) file_get_contents($path), true) : [];
    foreach (['ru', 'en'] as $lang) {
        $fields = $defaults[$lang]['flow'] ?? [];
        if (!is_array($fields)) continue;
        foreach ($fields as $suffix => $default) {
            $meta = 'bng_' . $lang . '_flowsheet_' . $suffix;
            $key = 'field_bng_' . $lang . '_flowsheet_' . $suffix;
            if (!is_array($default)) {
                if (!metadata_exists('post', $post_id, $meta)) {
                    update_field($key, (string) $default, $post_id);
                }
                continue;
            }
            $marker = '_bng_flowsheet_seeded_' . $lang . '_' . $suffix;
            if (metadata_exists('post', $post_id, $marker)) continue;
            $count = (int) get_post_meta($post_id, $meta, true);
            $filled = false;
            for ($i = 0; $i < max($count, count($default)); $i++) {
                for ($col = 0; $col < ($suffix === 'routes' ? 4 : 1); $col++) {
                    $value = get_post_meta($post_id, $meta . '_' . $i . '_c' . $col, true);
                    if (is_scalar($value) && trim((string) $value) !== '') {
                        $filled = true;
                        break 2;
                    }
                }
            }
            if (!$filled) {
                $rows = [];
                foreach ($default as $entry) {
                    $cells = is_array($entry) ? array_values($entry) : [(string) $entry];
                    $row = [];
                    foreach ($cells as $i => $cell) {
                        $row[$key . '_' . $i] = (string) $cell;
                    }
                    $rows[] = $row;
                }
                update_field($key, $rows, $post_id);
            }
            update_post_meta($post_id, $marker, '1');
        }

        $name = 'bng_' . $lang . '_flowsheet_streams';
        $marker = '_bng_flowsheet_streams_seeded_' . $lang;
        if (metadata_exists('post', $post_id, $marker)) continue;
        $entries = is_array($stream_defaults[$lang] ?? null) ? $stream_defaults[$lang] : [];
        $count = (int) get_post_meta($post_id, $name, true);
        $filled = false;
        for ($i = 0; $i < max($count, count($entries)); $i++) {
            $value = get_post_meta($post_id, $name . '_' . $i . '_symbol', true);
            if (is_scalar($value) && trim((string) $value) !== '') {
                $filled = true;
                break;
            }
        }
        if (!$filled && $entries) {
            $rows = [];
            foreach ($entries as $entry) {
                $prefix = 'field_bng_' . $lang . '_flowsheet_stream_';
                $products = [];
                foreach (($entry['products'] ?? []) as $product) {
                    $products[] = [
                        $prefix . 'product_formula' => (string) ($product['formula'] ?? ''),
                        $prefix . 'product_name' => (string) ($product['name'] ?? ''),
                    ];
                }
                $rows[] = [
                    $prefix . 'symbol' => (string) ($entry['symbol'] ?? ''),
                    $prefix . 'atomic' => (string) ($entry['atomic'] ?? ''),
                    $prefix . 'name' => (string) ($entry['name'] ?? ''),
                    $prefix . 'count' => (string) ($entry['count'] ?? ''),
                    $prefix . 'buyers' => (string) ($entry['buyers'] ?? ''),
                    $prefix . 'products' => $products,
                ];
            }
            update_field('field_bng_' . $lang . '_flowsheet_streams', $rows, $post_id);
        }
        update_post_meta($post_id, $marker, '1');
    }
}, 80);

/**
 * Product catalogue: initialize every source product, grade, specification and
 * measured parameter in ACF once, without overwriting content on subsequent visits.
 * Source data is the exact RU/EN client bundle extracted in source-content.json.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('products');
    if (!$post_id) return;
    $source = bng_source();
    foreach (['ru', 'en'] as $locale) {
        $part = $source['copy'][$locale]['products'] ?? [];
        foreach ($part as $suffix => $default) {
            $key = 'field_bng_' . $locale . '_products_' . $suffix;
            $meta = 'bng_' . $locale . '_products_' . $suffix;
            if (is_string($default)) {
                if (!metadata_exists('post', $post_id, $meta)) {
                    update_field($key, $default, $post_id);
                }
                continue;
            }
            if (!is_array($default)) continue;
            $marker = '_bng_products_seeded_' . $locale . '_' . $suffix;
            if (metadata_exists('post', $post_id, $marker)) continue;
            $existing = get_post_meta($post_id, $meta, true);
            $count = is_numeric($existing) ? (int) $existing : 0;
            $has_content = false;
            for ($i = 0; $i < max($count, count($default)); $i++) {
                for ($c = 0; $c < ($suffix === 'cards' ? 4 : 1); $c++) {
                    $saved = get_post_meta($post_id, $meta . '_' . $i . '_c' . $c, true);
                    if (is_scalar($saved) && trim((string) $saved) !== '') {
                        $has_content = true;
                        break 2;
                    }
                }
            }
            if (!$has_content) {
                $rows = [];
                foreach ($default as $item) {
                    $cells = is_array($item) ? array_values($item) : [(string) $item];
                    $row = [];
                    foreach ($cells as $i => $cell) $row[$key . '_' . $i] = (string) $cell;
                    $rows[] = $row;
                }
                update_field($key, $rows, $post_id);
            }
            update_post_meta($post_id, $marker, '1');
        }
        $meta = 'bng_' . $locale . '_products_catalog';
        $marker = '_bng_products_catalog_seeded_' . $locale;
        if (metadata_exists('post', $post_id, $marker)) continue;
        $already = (int) get_post_meta($post_id, $meta, true);
        $has_catalog = false;
        for ($i = 0; $i < max(10, $already); $i++) {
            $code = get_post_meta($post_id, $meta . '_' . $i . '_code', true);
            if (is_scalar($code) && trim((string) $code) !== '') {
                $has_catalog = true;
                break;
            }
        }
        if (!$has_catalog) {
            $prefix = 'field_bng_' . $locale . '_product_';
            $rows = [];
            foreach (($source['products'] ?? []) as $product) {
                $grade_rows = [];
                foreach (($source['grades'][$product['code']] ?? []) as $grade) {
                    $params = [];
                    foreach (($grade['params'] ?? []) as $param) {
                        $translated = $param[$locale] ?? [];
                        $params[] = [
                            $prefix . 'param_label' => (string) ($translated['k'] ?? ''),
                            $prefix . 'param_value' => (string) ($translated['v'] ?? ''),
                            $prefix . 'param_unit' => (string) ($translated['u'] ?? ''),
                        ];
                    }
                    $grade_rows[] = [
                        $prefix . 'grade_code' => (string) ($grade['code'] ?? ''),
                        $prefix . 'grade_name' => (string) ($grade[$locale]['name'] ?? ''),
                        $prefix . 'grade_source_file' => (string) ($grade['file'] ?? ''),
                        $prefix . 'grade_params' => $params,
                    ];
                }
                $rows[] = [
                    $prefix . 'code' => (string) ($product['code'] ?? ''),
                    $prefix . 'name' => (string) ($product[$locale]['name'] ?? ''),
                    $prefix . 'formula' => (string) ($product['formula'] ?? ''),
                    $prefix . 'segment' => (string) ($product['seg'] ?? ''),
                    $prefix . 'stream' => (string) ($product['stream'] ?? ''),
                    $prefix . 'buyer' => (string) ($product[$locale]['buyer'] ?? ''),
                    $prefix . 'status' => (string) ($product[$locale]['status'] ?? ''),
                    $prefix . 'grades' => $grade_rows,
                ];
            }
            if ($rows) update_field($prefix . 'catalog', $rows, $post_id);
        }
        update_post_meta($post_id, $marker, '1');
    }
}, 90);

/**
 * Milestones: import the complete approved RU/EN timeline to the canonical
 * bng_section record. Existing editor content remains authoritative.
 * Repair legacy repeaters with empty saved rows once; never re-seed deliberate
 * deletions after an import marker has been recorded.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $post_id = bng_section_id('milestones');
    if (!$post_id) return;
    $copy = bng_source()['copy'] ?? [];
    foreach (['ru', 'en'] as $locale) {
        $fields = $copy[$locale]['milestones'] ?? [];
        if (!is_array($fields)) continue;
        foreach ($fields as $slug => $default) {
            $name = 'bng_' . $locale . '_milestones_' . $slug;
            $key = 'field_bng_' . $locale . '_milestones_' . $slug;
            if (!is_array($default)) {
                if (!metadata_exists('post', $post_id, $name)) {
                    update_field($key, (string) $default, $post_id);
                }
                continue;
            }

            $marker = '_bng_milestones_seeded_' . $locale . '_' . $slug;
            if (metadata_exists('post', $post_id, $marker)) continue;
            $saved_count = (int) get_post_meta($post_id, $name, true);
            $has_editor_values = false;
            for ($i = 0; $i < max($saved_count, count($default)); $i++) {
                foreach (['c0', 'c1', 'c2'] as $column) {
                    $stored = get_post_meta($post_id, $name . '_' . $i . '_' . $column, true);
                    if (is_scalar($stored) && trim((string) $stored) !== '') {
                        $has_editor_values = true;
                        break 2;
                    }
                }
            }
            if (!$has_editor_values) {
                $rows = [];
                foreach ($default as $entry) {
                    $parts = is_array($entry) ? array_values($entry) : [(string) $entry];
                    $rows[] = [
                        $key . '_0' => (string) ($parts[0] ?? ''),
                        $key . '_1' => (string) ($parts[1] ?? ''),
                        $key . '_2' => (string) ($parts[2] ?? ''),
                    ];
                }
                if (!update_field($key, $rows, $post_id)) continue;
            }
            update_post_meta($post_id, $marker, '1');
        }
    }
}, 100);

/**
 * Seed News and Company content in both languages from approved source.
 * Existing filled rows remain untouched. Once processed, markers prevent
 * intentional subsequent row deletions from being restored automatically.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('edit_pages') || !function_exists('update_field')) return;
    $copy = bng_source()['copy'] ?? [];
    foreach (['news','company'] as $section) {
        $post_id = bng_section_id($section);
        if (!$post_id) continue;
        foreach (['ru','en'] as $locale) {
            $part = $copy[$locale][$section] ?? [];
            if (!is_array($part)) continue;
            foreach ($part as $slug => $default) {
                $name = 'bng_' . $locale . '_' . $section . '_' . $slug;
                $key = 'field_bng_' . $locale . '_' . $section . '_' . $slug;
                if (!is_array($default)) {
                    if (!metadata_exists('post', $post_id, $name)) {
                        update_field($key, (string) $default, $post_id);
                    }
                    continue;
                }
                $marker = '_bng_seeded_' . $section . '_' . $locale . '_' . $slug;
                if (metadata_exists('post', $post_id, $marker)) continue;
                $saved_count = (int) get_post_meta($post_id, $name, true);
                $has_content = false;
                for ($i = 0; $i < max($saved_count, count($default)); $i++) {
                    foreach (['c0','c1','c2','c3'] as $column) {
                        $value = get_post_meta($post_id, $name . '_' . $i . '_' . $column, true);
                        if (is_scalar($value) && trim((string) $value) !== '') {
                            $has_content = true;
                            break 2;
                        }
                    }
                }
                if (!$has_content) {
                    $rows = [];
                    foreach ($default as $entry) {
                        $cells = is_array($entry) ? array_values($entry) : [(string) $entry];
                        $row = [];
                        foreach ($cells as $idx => $value) {
                            $row[$key . '_' . $idx] = (string) $value;
                        }
                        $rows[] = $row;
                    }
                    if ($rows) update_field($key, $rows, $post_id);
                }
                update_post_meta($post_id, $marker, '1');
            }
        }
    }
}, 110);

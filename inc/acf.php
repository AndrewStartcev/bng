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

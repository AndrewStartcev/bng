# PDF-спецификации BNG — 26.10.2026

Работа выполнена через авторизованный Brave пользователя на dev-сайте https://bng.web-projects-it.ru/.

## Выполнено

- Загружены 18 предоставленных английских PDF в медиабиблиотеку.
- В канонической записи «Продукты» (post=10) привязаны 18 PDF в каждом языке: RU и EN, всего 36 файловых полей.
- По явному согласованию пользователя добавлена PS-K04.C отдельной маркой с восемью характеристиками на RU и EN. Остальные существующие названия, характеристики, статусы и коды не изменялись.
- PDF PS-K06.A сопоставлен единственной существующей марке PS-K06 (карбонат калия), код каталога сохранён.
- PDF PS-Al01.B подключён к ранее не имевшей файла марке.
- Для PS-Si01.A/B PDF в предоставленном наборе нет; оставлены существующие DOCX. Сельскохозяйственный SiO₂ остался без марки и со статусом подготовки.
- Код темы и production не менялись. Изменения сохранены в WordPress/ACF на dev.

## Проверка

Все 10 продуктов открыты на RU и EN. Для каждого языка подтверждены 18 PDF-ссылок и 2 прежние DOCX-ссылки. Все 18 уникальных PDF скачаны: HTTP 200, сигнатура %PDF-, SHA-256 совпадает с локальным оригиналом. До сохранения сравнение существующих видимых полей ACF не выявило изменений; добавлены только поля новой марки и привязки файлов. Исходное состояние полей сохранено локально.

## Примечания

В PDF PS-K04.C есть пометка о замене PS-K04.B Rev A (09.2026). Пользователь согласовал добавление C отдельной маркой; B сохранена. Статические счётчики главной страницы не пересчитываются из каталога (после добавления C у калия 14 марок, исходная статическая подпись главной — 13); это отдельная правка темы, вне загрузки документов.

## Проверенные файлы

| URL | HTTP | Совпадение оригиналу | SHA-256 |
| --- | --- | --- | --- |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-al01.a-alumina-eng-metallurgical-grade.pdf | 200 | да | 4276f595fd9d5d495753e288ef7d1f51f1ea31a5ed82d5a8d167b673369a91e0 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-al01.b-alumina-trihydrate-chemical-grade.pdf | 200 | да | cb8b5ca26242a622ad3c56d86e97b9f55f97ee7157cea9c65fcee894bee77af1 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-fe01-iron-oxide-pigment-eng.pdf | 200 | да | 90e174b66ecc497e507a98a4752e4f342634b2d677d9a52c839a30a824b80399 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-fe02-iron-ore-concentrate-eng.pdf | 200 | да | 04c807d07a133c7f06be6c9ced2f90bbde1da37cedcd58afa087ba69ef2a97a1 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k01.a-potassium-sulphate-standard-eng.pdf | 200 | да | 6c6bf599ab46bbf615800fee0b134d8179643dcb6d7028c2c2dab71f28f7e1c6 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k01.b-potassium-sulphate-granular-eng.pdf | 200 | да | ecb271ee094f4f26d80eb7469db70237ead89f97676fb5b001019f83c958a010 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k01.c-potassium-sulphate-ws-eng.pdf | 200 | да | cce4e926e66ba6025833727c1451a46db3428e484ecb7fc31e86a4f4b6b5bfa9 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k02-potassium-nitrate-ws-eng.pdf | 200 | да | 428a7dedc48c184675bd0d298a1266966d5db4eacac552dba5e4681f62d1a141 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k03.a-potassium-magnesium-sulphate-standard-eng.pdf | 200 | да | 169e7f2d11c7ab79a1e23f8f063db322875e24a95b119bd5e7c6b35178ac9ec0 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k03.b-potassium-magnesium-sulphate-granular-eng.pdf | 200 | да | d8b9470b60672ac17a5716b4cf4e6e548d14d4f938080682ed22510e334f5f75 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k03.c-potassium-magnesium-sulphate-ws-eng.pdf | 200 | да | 09dc2bb75f6f1b3aa6e9359e3d73525bc93c88528beb9beabec66e3b82e18f55 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k03.d-potassium-magnesium-sulphate-granular-premium-eng.pdf | 200 | да | 4baecd84543e0782a8085f9f7f574fe2f159049c66717158d1155064d22a0925 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k04.a-potassium-silicate-ws-hydrated-eng.pdf | 200 | да | 215e9e44f714bb418b9101a179e6b335b48e9cb26f7cf8d74ce7eaacb1122a79 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k04.b-potassium-silicate-granular-eng.pdf | 200 | да | 296cd0d665bdba8c51d4493cca25c18ca0f7170e88dc354b21a4ecbbf53a668a |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k04.c-potassium-silicate-crystal-granular-eng.pdf | 200 | да | 7f70105a4b3bd7313813558227dc707319395a6dc1f49ee7cf6916a5ac777e51 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k06.a-potassium-carbonate-ws-technical-eng.pdf | 200 | да | b88d72fbf26f0c9166d6591390786b9825b9636074419ed73fc8c80ee9bc68e3 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k07.a-potassium-hydroxide-flakes-eng.pdf | 200 | да | adcae9caed067a57773d3a802f3c44e319eef11b6c5a1a4a252d68df7f7ae9e2 |
| https://bng.web-projects-it.ru/wp-content/uploads/2026/10/ps-k07.b-potassium-hydroxide-pellets-eng.pdf | 200 | да | 44109b653c3536d9af45e311ba552a960f61f24753abc9fe0498480c5672747e |

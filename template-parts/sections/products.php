<?php
defined('ABSPATH') || exit;
$source = bng_source();
$labels = bng_copy('', $lang);
$spec_files = bng_spec_files((int) $post_id, $lang);
?>
<section class="section product-page">
    <?php bng_render_lead($copy, (int) $post_id); ?>
    <div class="notice-panel"><strong><?php echo esc_html($copy['statusLabel'] ?? ''); ?></strong><p><?php echo esc_html($copy['statusBody'] ?? ''); ?></p></div>
    <div class="product-filters" role="group" aria-label="<?php echo esc_attr($copy['filter'] ?? 'Filter'); ?>">
        <span class="kicker"><?php echo esc_html($copy['filter'] ?? ''); ?></span>
        <?php foreach (['all', 'agro', 'chemicals', 'metals'] as $segment) : ?>
            <button class="product-filters__button<?php echo $segment === 'all' ? ' is-active' : ''; ?>" type="button" data-bng-filter="<?php echo esc_attr($segment); ?>" aria-pressed="<?php echo $segment === 'all' ? 'true' : 'false'; ?>"><?php echo esc_html($labels['seg'][$segment] ?? $segment); ?></button>
        <?php endforeach; ?>
        <span class="product-filters__count" data-bng-count><?php echo esc_html(count($source['products'] ?? [])); ?> / 10</span>
    </div>
    <div class="product-table-scroll">
        <table class="product-table">
            <thead><tr>
                <th scope="col"><?php echo esc_html($copy['colProduct'] ?? 'Product'); ?></th>
                <th scope="col"><?php echo esc_html($copy['colFormula'] ?? 'Formula'); ?></th>
                <th scope="col"><?php echo esc_html($copy['colSegment'] ?? 'Segment'); ?></th>
                <th scope="col"><?php echo esc_html($copy['colBuyer'] ?? 'Buyers'); ?></th>
                <th scope="col"><?php echo esc_html($copy['colStatus'] ?? 'Status'); ?></th>
            </tr></thead>
            <tbody>
            <?php foreach (($source['products'] ?? []) as $product) :
                $product_id = (string) ($product['code'] ?? '');
                $grades = $source['grades'][$product_id] ?? [];
                $info = $product[$lang] ?? [];
            ?>
                <tr data-bng-segment="<?php echo esc_attr($product['seg'] ?? ''); ?>">
                    <td><details class="product-details"><summary><span class="element-chip"><?php echo esc_html($product['stream'] ?? ''); ?></span><span><?php echo esc_html($info['name'] ?? ''); ?><small><?php echo esc_html($product_id); ?> · <?php echo esc_html(count($grades)); ?> <?php echo esc_html($copy['colGrades'] ?? 'Grades'); ?></small></span></summary>
                        <?php if ($grades) : ?>
                            <div class="grade-list">
                            <?php foreach ($grades as $grade) :
                                $code = (string) ($grade['code'] ?? '');
                                $file_url = $spec_files[$code] ?? '';
                            ?>
                                <div class="grade-card">
                                    <span class="kicker"><?php echo esc_html($code); ?></span>
                                    <h3><?php echo esc_html($grade[$lang]['name'] ?? $code); ?></h3>
                                    <dl class="grade-spec">
                                        <?php foreach (($grade['params'] ?? []) as $param) : $p = $param[$lang] ?? []; ?>
                                            <div><dt><?php echo esc_html($p['k'] ?? ''); ?></dt><dd><?php echo esc_html($p['v'] ?? ''); ?> <?php echo esc_html($p['u'] ?? ''); ?></dd></div>
                                        <?php endforeach; ?>
                                    </dl>
                                    <?php if ($file_url) : ?>
                                        <a class="button button--sm" href="<?php echo esc_url($file_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($copy['download'] ?? 'Download'); ?> ↓</a>
                                    <?php else : ?><span class="grade-card__pending"><?php echo esc_html($lang === 'en' ? 'Specification on request' : 'Спецификация по запросу'); ?></span><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </details></td>
                    <td><?php echo esc_html($product['formula'] ?? ''); ?></td>
                    <td><?php echo esc_html($labels['seg'][$product['seg'] ?? ''] ?? ''); ?></td>
                    <td><?php echo esc_html($info['buyer'] ?? ''); ?></td>
                    <td><?php echo esc_html($info['status'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted"><?php echo esc_html($copy['gradeFoot'] ?? ''); ?></p>
    <div class="segment-grid">
        <?php foreach (($copy['cards'] ?? []) as $card) : ?>
            <article class="segment-card"><h3><?php echo esc_html($card[0] ?? ''); ?></h3><span class="kicker"><?php echo esc_html($card[1] ?? ''); ?></span><p><?php echo esc_html($card[2] ?? ''); ?></p><strong><?php echo esc_html($card[3] ?? ''); ?></strong></article>
        <?php endforeach; ?>
    </div>
</section>

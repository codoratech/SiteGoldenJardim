<?php
function site_render_services($items) {
    foreach ($items as $index=>$item) { ?>
    <div class="service-card reveal" style="transition-delay:<?= (int) $index * 80 ?>ms">
      <?php if ($item['img']) { ?><img src="<?= site_escape($item['img']) ?>" alt="" loading="lazy" /><?php } ?>
      <div class="grad"></div>
      <div class="top"><span><?= site_escape(sprintf('%02d / %02d', $index + 1, count($items))) ?></span><span aria-hidden="true"><?= site_escape($item['icon']) ?></span></div>
      <div class="bottom"><h3><?= site_escape($item['pt'][0]) ?></h3><p><?= site_escape($item['pt'][1]) ?></p><div class="rule"></div></div>
    </div>
    <?php }
}

function site_render_projects($items) {
    foreach ($items as $index=>$item) { $text = $item['pt']; ?>
    <figure class="gallery-item reveal<?= $item['img'] ? '' : ' gallery-item-no-image' ?>" style="transition-delay:<?= (int) $index * 100 ?>ms">
      <?php if ($item['img']) { ?><img src="<?= site_escape($item['img']) ?>" alt="<?= site_escape($text['alt']) ?>" loading="lazy" /><?php } ?>
      <div class="grad"></div>
      <figcaption class="cap"><div>
        <?php if ($text['category'] !== '') { ?><span class="tag"><?= site_escape($text['category']) ?></span><?php } ?>
        <h3><?= site_escape($text['title']) ?></h3>
        <?php if ($text['description'] !== '') { ?><p class="project-description"><?= site_escape($text['description']) ?></p><?php } ?>
        <?php if ($text['location'] !== '') { ?><span class="project-location"><?= site_escape($text['location']) ?></span><?php } ?>
      </div><span class="arrow" aria-hidden="true">↗</span></figcaption>
    </figure>
    <?php }
}

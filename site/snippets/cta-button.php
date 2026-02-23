<?php
$link  = $link  ?? '#';
$label = $label ?? 'Mehr dazu';
$icon  = $icon  ?? null;
?>
<a href="<?= esc($link) ?>" class="cta-button">
    <?php if ($icon): ?>
        <span class="cta-icon">
            <?= $icon ?>
        </span>
    <?php endif ?>

    <span><?= esc($label) ?></span>

    <?= snippet('arrow-right') ?>
</a>

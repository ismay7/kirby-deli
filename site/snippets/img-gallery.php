<?php
$images = $page->imgGallery()->toFiles();

if ($images->isNotEmpty()):
  $count = $images->count();
?>
  <div class="img-gallery" data-aos="fade-right">
    <div class="img-slider" data-slider="imgslider">

      <?php foreach ($images as $index => $image): ?>
        <figure class="img-slide<?= $index === 0 ? ' is-active' : '' ?>">
          <img
            src="<?= $image->thumb('card')->url() ?>"
            alt="<?= esc($image->alt()->or($image->filename())) ?>"
            loading="<?= $index === 0 ? 'eager' : 'lazy' ?>"
            decoding="async"
          >
        </figure>
      <?php endforeach; ?>

      <?php if ($count > 1): ?>
        <button class="img-slider-btn img-slider-prev" type="button" aria-label="Zurück">‹</button>
        <button class="img-slider-btn img-slider-next" type="button" aria-label="Weiter">›</button>

        <div class="img-slider-dots" role="tablist" aria-label="Galerie Navigation">
          <?php foreach ($images as $index => $image): ?>
            <button
              class="img-slider-dot<?= $index === 0 ? ' is-active' : '' ?>"
              type="button"
              aria-label="Bild <?= (int)$index + 1 ?>"
              aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
            ></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
<?php endif; ?>

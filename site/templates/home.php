<?php snippet('header') ?>
<?php snippet('nav') ?>

<?php if ($bg = $page->background()->toFile()): ?>
<section id="hero" style="background-image: url('<?= $bg->thumb('hero')->url() ?>');">
<?php endif ?>

    <div id="hero-logo-wrap">
        <img id="logo-top" src="assets/img/transp-logo-risto.png" alt="Logo La Rotonda">
        <p class="sub">&nbsp;viva <span id="typer"></span></p>
    </div>
    <?php snippet(name: 'arrow-down') ?>
</section>

<section id="about">
    <div class="card" data-aos="zoom-in-up">
        <div class="about-text">
            <?= $page->aboutText1()->kt() ?>
        </div>
    </div>
    
    <div class="card" data-aos="zoom-in-up">
        <?= $page->aboutText2()->kt() ?>
    </div>
</section>

<div class="section-separator"></div>

<section id="specials">
    <?php snippet(name: 'img-gallery') ?>

    <div class="text-block" data-aos="fade-left">
        <?= $page->specialities()->kt() ?>

        <?php snippet('cta-button', [
            'icon'  => '',
            'label' => 'MENÜS',
            'link'  => page('menu')->url()
        ]) ?>
    </div>
</section>

<div class="section-separator"></div>

<?php
// FIXED DAYS (Mo–So) from site fields
$days = [
  ['key' => 'mon', 'label' => 'Montag'],
  ['key' => 'tue', 'label' => 'Dienstag'],
  ['key' => 'wed', 'label' => 'Mittwoch'],
  ['key' => 'thu', 'label' => 'Donnerstag'],
  ['key' => 'fri', 'label' => 'Freitag'],
  ['key' => 'sat', 'label' => 'Samstag'],
  ['key' => 'sun', 'label' => 'Sonntag'],
];

// Sonderöffnungszeiten
$special = $site->specialOpeningHours()->toStructure()->sortBy('date', 'asc');

// Betriebsferien
$holidayEnabled = $site->holidayEnabled()->toBool();
$holidayFrom    = $site->holidayFrom()->isNotEmpty() ? $site->holidayFrom()->toDate('d.m.Y') : null;
$holidayTo      = $site->holidayTo()->isNotEmpty() ? $site->holidayTo()->toDate('d.m.Y') : null;
$holidayNote    = trim($site->holidayNote()->value());
?>

<section id="hours-section">
  <div id="hours-card" class="card" data-aos="zoom-in-up">
    <h2 class="hours-title">Öffnungszeiten</h2>

    <!-- Öffnungszeiten (Mo–So fix) -->
    <dl class="hours-grid">
      <?php foreach ($days as $d): ?>
        <?php
          $k = $d['key'];

          // read fields safely from site content
          $closed = $site->content()->get($k . 'Closed')->toBool();
          $times  = trim($site->content()->get($k . 'Times')->value());
          $note   = trim($site->content()->get($k . 'Note')->value());
        ?>
        <dt class="hours-day"><?= esc($d['label']) ?></dt>
        <dd class="hours-time">
          <?php if ($closed): ?>
            <span class="hours-closed">geschlossen</span>
          <?php else: ?>
            <?= esc($times ?: '—') ?>
            <?php if (!empty($note)): ?>
              <span class="hours-note"><?= esc($note) ?></span>
            <?php endif ?>
          <?php endif ?>
        </dd>
      <?php endforeach ?>
    </dl>

    <!-- Sonderöffnungszeiten -->
    <?php if ($special->isNotEmpty()): ?>
      <div class="hours-divider"></div>
      <p class="hours-subtitle">Sonderöffnungszeiten</p>

      <dl class="hours-grid">
        <?php foreach ($special as $row): ?>
          <dt class="hours-day">
            <?= esc($row->date()->toDate('d.m.Y')) ?>
            <?php if ($row->note()->isNotEmpty()): ?>
              <span class="hours-note"><?= esc($row->note()->value()) ?></span>
            <?php endif ?>
          </dt>
          <dd class="hours-time">
            <?php if ($row->closed()->toBool()): ?>
              <span class="hours-closed">geschlossen</span>
            <?php else: ?>
              <?= esc($row->times()->value()) ?>
            <?php endif ?>
          </dd>
        <?php endforeach ?>
      </dl>
    <?php endif ?>

    <!-- Betriebsferien -->
    <?php if ($holidayEnabled && $holidayFrom && $holidayTo): ?>
      <div class="hours-divider"></div>
      <p class="hours-subtitle">Betriebsferien</p>

      <p class="hours-holiday">
        <?= esc($holidayFrom) ?> – <?= esc($holidayTo) ?>
        <?php if (!empty($holidayNote)): ?>
          <span class="hours-note"><?= esc($holidayNote) ?></span>
        <?php endif ?>
      </p>
    <?php endif ?>

  </div>

  <div id="kontakt" class="card" data-aos="zoom-in-up">
    <h2 class="hours-title"><?= esc($site->addressTitle()->or('Adresse')) ?></h2>

    <p class="address-block">
      <strong><?= esc($site->addressName()) ?></strong><br>
      <?= esc($site->addressStreet()) ?><br>
      <?= esc($site->addressZipCity()) ?>
    </p>

    <?php if ($site->addressPhone()->isNotEmpty()): ?>
      <p class="address-line">
        <a href="tel:<?= esc(preg_replace('/\s+/', '', $site->addressPhone()->value())) ?>">
          <?= esc($site->addressPhone()) ?>
        </a>
      </p>
    <?php endif ?>

    <?php if ($site->addressEmail()->isNotEmpty()): ?>
      <p class="address-line">
        <a href="mailto:<?= esc($site->addressEmail()) ?>">
          <?= esc($site->addressEmail()) ?>
        </a>
      </p>
    <?php endif ?>

    <div id="map" class="map-embed">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d676.1543576246323!2d8.5210602!3d47.3218119!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x479008292f77d41f%3A0xc2758372965fda9!2sIm%20Sihlhof%201%2C%208134%20Adliswil!5e0!3m2!1sde!2sch!4v1765570687824!5m2!1sde!2sch"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <a
      href="https://maps.google.com/?q=Im+Sihlhof+1+8134+Adliswil"
      target="_blank"
      rel="noopener"
      class="map-link">
      Route in Google Maps öffnen →
    </a>
  </div>
</section>

<?php snippet('cursor-effects') ?>
<?php snippet(name: 'footer') ?>

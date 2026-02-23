
<!-- MARQUEE -->
<?php
$marqueeField   = $site->marqueeText(); // Feld-Objekt
$marqueeEnabled = $site->marqueeEnabled()->toBool();
$hasMarquee     = $marqueeEnabled && $marqueeField->isNotEmpty();
?>

<?php if ($hasMarquee): ?>
  <div id="marquee">
    <div id="track">
      <span><?= esc($marqueeField) ?></span> 
      <span><?= esc($marqueeField) ?></span>
    </div>
  </div>
<?php endif ?>
<!-- MARQUEE end -->


<!-- NAVIGATION -->
<?php
$items = $pages->listed();
// only show the menu if items are available
if($items->isNotEmpty()): ?>
<nav id="nav-menu"class="<?= $hasMarquee ? 'has-marquee' : '' ?>">
    <div id="menu-head">
        <div id="logo">
            <a href="<?= $site->url() ?>"><img src="../../assets/img/transp-logo-solo.png" alt="Logo"></a>
        </div>
        <!-- menu-btn only on mobile -->
        <button id="menu-btn">
            <div id="nav-icon">
                <span class="nav-span"></span>
                <span class="nav-span"></span>
                <span class="nav-span"></span>
                <span class="nav-span"></span>
            </div>
        </button>
    </div>

        <ul>
            <?php foreach($items as $item): ?>
            <li>
              <a<?php e($item->isOpen(), ' class="active"') ?> href="<?= $item->url() ?>">
                <?= $item->title()->html() ?>
              </a>
            </li>
            <?php endforeach ?>
            <li><a href="<?= url() ?>#kontakt">Kontakt</a></li>
            <!-- <li><a href="https://shop.restaurant-larotonda.ch">Delivery</a></li> -->
            <li><a href="" class="glow">Reservieren</a></li>
        </ul>
</nav>
<?php endif ?>
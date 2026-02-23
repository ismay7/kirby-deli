<?php snippet('header') ?>
<?php snippet('nav') ?>
<?php snippet('cursor-effects') ?>

<div class="page-wrap">
    <div class="title-box">
        <h1><?= kti($page->myTitle()) ?></h1>
        <?= kt($page->introText()) ?>
        <hr>
    </div>

    <section id="menus">

        <div class="card card-menu" data-aos="fade-right">
            <div class="menu-row">
                <div class="img-menu">
                    <?php if ($img = $page->imgDinner()->toFile()): ?>
                    <img src="<?= $img->thumb('card')->url() ?>" alt="">
                    <?php endif ?>
                </div>
                <div class="about-menu">
                    <?= $page->aboutDinner()->kt() ?>
                </div>
            </div>
            <div class="menu-footer">
                
                <?php $pdfDinner = $page->pdfDinner()->toFile(); ?>             
                <?php if ($pdfDinner): ?>
                <?php snippet('cta-button', [
                    'icon'  => '',
                    'label' => 'Ansehen (PDF)',
                    'link'  => $pdfDinner->url()
                ]) ?>
                <?php endif ?>

            </div>
        </div>

        <div class="card card-menu" data-aos="fade-left">
            <div class="menu-row">
                <div class="img-menu">
                    <?php if ($img = $page->imgLunch()->toFile()): ?>
                    <img src="<?= $img->thumb('card')->url() ?>" alt="">
                    <?php endif ?>
                </div>
                <div class="about-menu">
                    <?= $page->aboutLunch()->kt() ?>
                </div>
            </div>
            <div class="menu-footer">
                
                <?php $pdfLunch = $page->pdfLunch()->toFile(); ?>             
                <?php if ($pdfLunch): ?>
                <?php snippet('cta-button', [
                    'icon'  => '',
                    'label' => 'Ansehen (PDF)',
                    'link'  => $pdfLunch->url()
                ]) ?>
                <?php endif ?>

            </div>
        </div>

        <div class="card card-menu" data-aos="fade-right">
            <div class="menu-row">
                <div class="img-menu">
                    <?php if ($img = $page->imgSeason()->toFile()): ?>
                    <img src="<?= $img->thumb('card')->url() ?>" alt="">
                    <?php endif ?>
                </div>
                <div class="about-menu">
                    <?= $page->aboutSeason()->kt() ?>
                </div>
            </div>
            <div class="menu-footer">

                <?php $pdfSeason = $page->pdfSeason()->toFile(); ?>             
                <?php if ($pdfSeason): ?>
                <?php snippet('cta-button', [
                    'icon'  => '',
                    'label' => 'Ansehen (PDF)',
                    'link'  => $pdfSeason->url()
                ]) ?>
                <?php endif ?>

            </div>
        </div>

        <div class="card card-menu" data-aos="fade-left">
            <div class="menu-row">
                <div class="img-menu">
                    <?php if ($img = $page->imgDrinks()->toFile()): ?>
                    <img src="<?= $img->thumb('card')->url() ?>" alt="">
                    <?php endif ?>
                </div>
                <div class="about-menu">
                    <?= $page->aboutDrinks()->kt() ?>
                </div>
            </div>
            <div class="menu-footer">

                <?php $pdfDrinks = $page->pdfDrinks()->toFile(); ?>             
                <?php if ($pdfDrinks): ?>
                <?php snippet('cta-button', [
                    'icon'  => '',
                    'label' => 'Ansehen (PDF)',
                    'link'  => $pdfDrinks->url()
                ]) ?>
                <?php endif ?>

            </div>
        </div>

    </section>
</div>

<?php snippet('footer') ?>
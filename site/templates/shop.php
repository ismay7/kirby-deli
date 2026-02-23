<?php snippet('header') ?>
<?php snippet('nav') ?>

<div id="shop-opening-data"
  data-opening-hours='<?= json_encode($page->openingHours()->toStructure()->toArray()) ?>'
  data-shop-closed='<?= json_encode($page->shopClosedToggle()->bool()) ?>'
></div>

<div id="pickup-data"
  data-pickup-discount-value="<?= $page->pickupDiscounts()->toStructure()->isNotEmpty() ? $page->pickupDiscounts()->toStructure()->first()->value()->value() : '0' ?>"
  data-pickup-discount-type="<?= $page->pickupDiscounts()->toStructure()->isNotEmpty() ? $page->pickupDiscounts()->toStructure()->first()->type()->value() : 'amount' ?>"
></div>

<!-- FIXIERTE KATEGORIEN-NAVIGATION (außerhalb von .page-container) -->
<div class="categories-container">
  <div class="categories-scroll">
    <button class="category-tab active" data-category="salate">Salate</button>
    <button class="category-tab" data-category="pizza">Pizza</button>
    <button class="category-tab" data-category="pasta">Pasta</button>
    <button class="category-tab" data-category="risotto">Risotto</button>
    <button class="category-tab" data-category="spezial">Spezialitäten</button>
    <button class="category-tab" data-category="getraenke">Getränke</button>
  </div>
</div>

<div class="page-container">
  <div class="main-content">
    <!-- Liefermethode-Auswahl -->
    <div class="zip-selection">
      <label for="shipping-method">Liefergebiet oder Abholung auswählen:</label>
      <select id="shipping-method" name="shipping-method" required>
        <option value="">Bitte auswählen...</option>
        <optgroup label="Lieferung (Mindestbestellwert beachten)">
          <option value="zone1" data-min-order="25" data-postcodes="8134,8041">
            Zone 1: Adliswil (8134), Leimbach (8041) – Mindestbestellwert: 25 CHF
          </option>
          <option value="zone2" data-min-order="30" data-postcodes="8038,8802">
            Zone 2: Wollishofen (8038), Kilchberg (8802) – Mindestbestellwert: 30 CHF
          </option>
          <option value="zone3" data-min-order="40" data-postcodes="8136,8135">
            Zone 3: Gattikon (8136), Langnau a.A. (8135) – Mindestbestellwert: 40 CHF
          </option>
          <option value="zone4" data-min-order="50" data-postcodes="8803,8800">
            Zone 4: Rüschlikon (8803), Thalwil (8800) – Mindestbestellwert: 50 CHF
          </option>
        </optgroup>
        <optgroup label="Abholung">
          <option value="pickup" data-min-order="0">
            Abholung vor Ort – kein Mindestbestellwert
          </option>
        </optgroup>
      </select>
      <div id="min-order-info">Mindestbestellwert für Ihre Zone: <span id="min-order-amount">0</span> CHF</div>
      <div id="pickup-discount-info"></div>
    </div>

    <!-- Produkte -->
    <div class="products">
      <?php
      $categories = [
        'salate' => $page->salateProducts()->toStructure(),
        'pizza' => $page->pizzaProducts()->toStructure(),
        'pasta' => $page->pastaProducts()->toStructure(),
        'risotto' => $page->risottoProducts()->toStructure(),
        'spezial' => $page->spezialProducts()->toStructure(),
        'getraenke' => $page->getraenkeProducts()->toStructure()
      ];
      $categoryLabels = [
        'salate' => 'Salate',
        'pizza' => 'Pizza',
        'pasta' => 'Pasta',
        'risotto' => 'Risotto',
        'spezial' => 'Spezialitäten',
        'getraenke' => 'Getränke'
      ];

      foreach ($categories as $categorySlug => $products): ?>
        <div class="category" data-category="<?= $categorySlug ?>">
          <h2><?= html($categoryLabels[$categorySlug]) ?></h2>
          <div class="products-grid">
            <?php foreach ($products as $product): ?>
              <div class="card product" data-has-toppings="<?= $product->toppings()->toBool() ? 'true' : 'false' ?>">
                <h3><?= $product->title() ?></h3>
                <p><?= $product->description() ?></p>
                <div class="variants">
                  <?php foreach ($product->variants()->toStructure() as $variant): ?>
                    <div class="variant">
                      <button class="add-to-cart"
                              data-product-id="<?= $product->id() ?>"
                              data-variant="<?= $variant->name() ?>"
                              data-price="<?= $variant->price()->toFloat() ?>">
                        <span><?= $variant->name() ?></span> <br>
                        <?= number_format($variant->price()->toFloat(), 2) ?> CHF
                      </button>
                    </div>
                  <?php endforeach ?>
                </div>
              </div>
            <?php endforeach ?>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>

  <!-- Warenkorb -->
  <div class="floating-cart">
    <!-- Kompakter Header für Mobile -->
    <div class="cart-header" id="cartHeader">
      <div class="cart-chevron-row">
        <i class="ri-arrow-up-s-line cart-chevron"></i>
      </div>
      <div class="cart-main-row">
        <div class="cart-compact">
          <div class="icon-with-badge">
            <i class="ri-shopping-cart-2-line cart-icon"></i>
            <span class="cart-badge" id="cartBadge">0</span>
          </div>
          <div class="cart-header-title">Warenkorb</div>
          <div class="cart-total-compact" id="cartTotalCompact">Total: 0.00 CHF</div>
        </div>
      </div>
    </div>

    <!-- Cart-Inhalt (standardmäßig versteckt auf Mobile) -->
    <div class="cart-items-container">
      <div class="cart-items"></div>
    </div>

    <div class="cart-total" id="cartTotalFull">Total: 0.00 CHF</div>
    <div id="min-order-warning" style="color: red; display: none;">
      Der Mindestbestellwert wurde noch nicht erreicht.
    </div>
    <button class="checkout">Zur Kasse <i class="ri-arrow-right-long-fill"></i></button>
  </div>
</div>

<!-- Toppings-Daten für JS -->
<script>
  window.toppingsData = <?= json_encode(
    $page->availableToppings()->toStructure()->isNotEmpty()
      ? $page->availableToppings()->toStructure()->map(function($topping) {
          return [
            'name' => $topping->name()->value(),
            'price32cm' => $topping->price32cm()->toFloat()
          ];
        })->data()
      : []
  ) ?>;
  window.toppingFactor40cm = <?= $page->toppingFactor40cm()->toFloat() ?: 1.25 ?>;
</script>

<?php snippet('toppings-popup') ?>
<?php snippet('cursor-effects') ?>
<?php snippet('footer') ?>
<script src="<?= url('assets/js/shop-opening-hours.js') ?>"></script>
<script src="<?= url('assets/js/shop.js') ?>"></script>

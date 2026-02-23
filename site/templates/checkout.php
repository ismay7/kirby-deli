<?php snippet('header') ?>
<?php snippet('nav') ?>

<div class="page-wrap-top">
    <div class="title-box">
        <h1><?= kti($page->myTitle()) ?></h1>
        <?= kt($page->introText()) ?>
        <hr>
    </div>
  <?php
  // Lieferzonen und PLZs (unverändert)
  $shippingZones = [
    'zone1' => ['name' => 'Zone 1', 'postcodes' => ['8134', '8041'], 'min_order' => 25],
    'zone2' => ['name' => 'Zone 2', 'postcodes' => ['8038', '8802'], 'min_order' => 30],
    'zone3' => ['name' => 'Zone 3', 'postcodes' => ['8136', '8135'], 'min_order' => 40],
    'zone4' => ['name' => 'Zone 4', 'postcodes' => ['8803', '8800'], 'min_order' => 50],
    'pickup' => ['name' => 'Abholung', 'postcodes' => [], 'min_order' => 0]
  ];
  $selectedShippingMethod = kirby()->request()->get('shipping');
  if (!$selectedShippingMethod || !isset($shippingZones[$selectedShippingMethod])) {
    go('/shop');
    exit;
  }

  // PLZ → Ort Mapping (unverändert)
  $postcodeToCity = [
    '8134' => 'Adliswil',
    '8041' => 'Zürich (Leimbach)',
    '8038' => 'Zürich (Wollishofen)',
    '8802' => 'Kilchberg',
    '8136' => 'Gattikon',
    '8135' => 'Langnau am Albis',
    '8803' => 'Rüschlikon',
    '8800' => 'Thalwil'
  ];
  $isPickup = ($selectedShippingMethod === 'pickup');
  $taxRates = $page->taxRates()->toStructure();
  $taxRate = $taxRates->isNotEmpty() ? $taxRates->first()->rate()->toFloat() / 100 : 0.026;
  $taxRateDisplay = $taxRates->isNotEmpty() ? $taxRates->first()->rate()->value() : 2.6;
  ?>

  <!-- Bestellzusammenfassung (unverändert) -->
  <div id="order-summary" class="card form-section">
    <h2><i class="ri-restaurant-line"></i> Deine Bestellung</h2>
    <div id="order-type-and-time">
      <div id="order-type">
        <?= $isPickup ? '<i class="ri-store-2-line"></i> Abholung vor Ort' : '<i class="ri-takeaway-fill"></i> Lieferung – ' . $shippingZones[$selectedShippingMethod]['name'] ?>
      </div>
      <div id="order-time">
        <i class="ri-time-line"></i>
        <select id="pickup-time" name="pickup_time" required>
          <option value="ASAP" selected>so bald wie möglich</option>
          <optgroup label="Vorbestellung (gleicher Tag)">
            <option value=""><?= date('H:i', strtotime('+30 minutes')) ?> Uhr (voraussichtlich)</option>
          </optgroup>
        </select>
      </div>
    </div>

    <div id="order-items" class="order-items-list"></div>
    <div class="order-totals">
      <div class="order-row">
        <span>Zwischensumme:</span>
        <span id="subtotal">0.00 CHF</span>
      </div>
      <div class="order-row">
        <span>inkl. <?= $taxRateDisplay ?>% MwSt.:</span>
        <span id="tax-amount">0.00 CHF</span>
      </div>
      <div class="order-row total-row">
        <strong>Total:</strong>
        <strong id="order-total">0.00 CHF</strong>
      </div>
    </div>
    <a href="<?= url('shop') ?>" class="back-to-cart">
      <i class="ri-arrow-go-back-fill"></i> zurück zum Warenkorb
    </a>
  </div>

  <!-- Warenkorb-Total (für JavaScript) -->
  <input type="hidden" id="cart-total" value="0">
  <input type="hidden" id="min-order-amount" value="<?= $shippingZones[$selectedShippingMethod]['min_order'] ?>">
  <input type="hidden" id="tax-rate" value="<?= $taxRate ?>">

  <form id="checkout-form" method="POST" action="/checkout">
    <!-- Liefermethode (versteckt) -->
    <input type="hidden" name="shipping_method" value="<?= html($selectedShippingMethod) ?>">

    <!-- Kundendaten (unverändert) -->
    <div class="form-section">
      <h2><i class="ri-user-line"></i> Kontakt</h2>
      <div class="field">
        <label for="email"><i class="ri-at-line"></i> E-Mail *</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="field">
        <label for="phone"><i class="ri-cellphone-fill"></i> Telefon (für Rückfragen) *</label>
        <input type="tel" id="phone" name="phone" required>
      </div>
      <div class="field checkbox-field">
        <input type="checkbox" id="create_account" name="create_account" value="1">
        <label for="create_account">Konto eröffnen (für zukünftige Bestellungen)</label>
      </div>
    </div>

    <!-- Lieferadresse (unverändert) -->
    <div class="form-section" <?= $isPickup ? 'style="display: none;"' : '' ?>>
      <h2><i class="ri-signpost-line"></i> Lieferadresse</h2>
      <div class="field-grid">
        <div class="field">
          <label for="delivery_firstname">Vorname *</label>
          <input type="text" id="delivery_firstname" name="delivery_firstname" required>
        </div>
        <div class="field">
          <label for="delivery_lastname">Nachname *</label>
          <input type="text" id="delivery_lastname" name="delivery_lastname" required>
        </div>
      </div>
      <div class="field">
        <label for="address"><i class="ri-home-4-line"></i> Adresse und Hausnummer *</label>
        <input type="text" id="address" name="address" required>
      </div>
      <div class="field-grid">
        <div class="field">
          <label for="postcode"><i class="ri-map-pin-line"></i> Postleitzahl *</label>
          <select id="postcode" name="postcode" required>
            <option value="">Bitte wählen...</option>
            <?php foreach ($shippingZones[$selectedShippingMethod]['postcodes'] ?? [] as $postcode): ?>
              <option value="<?= $postcode ?>"><?= $postcode ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="field">
          <label for="city"><i class="ri-building-line"></i> Ort *</label>
          <input type="text" id="city" name="city" required readonly>
        </div>
      </div>
    </div>

    <!-- Checkbox für abweichende Rechnungsadresse -->
    <?php if (!$isPickup): ?>
    <div id="different-billing-check" class="field checkbox-field">
      <input type="checkbox" id="different_billing" name="different_billing" value="1">
      <label for="different_billing">Abweichende Rechnungsadresse</label>
    </div>
    <?php endif; ?>

    <!-- Rechnungsadresse -->
    <div class="form-section" id="billing-address-section" <?= !$isPickup ? 'style="display: none;"' : '' ?>>
      <h2><i class="ri-bill-line"></i> Rechnungsadresse</h2>
      <div class="field-grid">
        <div class="field">
          <label for="billing_firstname">Vorname *</label>
          <input type="text" id="billing_firstname" name="billing_firstname" <?= $isPickup ? 'required' : '' ?>>
        </div>
        <div class="field">
          <label for="billing_lastname">Nachname *</label>
          <input type="text" id="billing_lastname" name="billing_lastname" <?= $isPickup ? 'required' : '' ?>>
        </div>
      </div>
      <div class="field">
        <label for="billing_address"><i class="ri-home-4-line"></i> Adresse und Hausnummer *</label>
        <input type="text" id="billing_address" name="billing_address" <?= $isPickup ? 'required' : '' ?>>
      </div>
      <div class="field-grid">
        <div class="field">
          <label for="billing_postcode"><i class="ri-map-pin-line"></i> Postleitzahl *</label>
          <input type="text" id="billing_postcode" name="billing_postcode" <?= $isPickup ? 'required' : '' ?>>
        </div>
        <div class="field">
          <label for="billing_city"><i class="ri-building-line"></i> Ort *</label>
          <input type="text" id="billing_city" name="billing_city" <?= $isPickup ? 'required' : '' ?>>
        </div>
      </div>
    </div>

    <!-- Zahlungsmethode -->
    <div class="form-section">
      <h2><i class="ri-wallet-line"></i> Zahlungsmethode</h2>
      <div class="field">
        <label for="payment_method">Zahlungsart *</label>
        <select id="payment_method" name="payment_method" required>
          <option value="">Bitte wählen...</option>
          <option value="CC">Kreditkarte/Debitkarte</option>
          <option value="TWINT">TWINT</option>
          <option value="COD">Bargeld/Terminal-Zahlung beim Erhalten</option>
        </select>
      </div>
    </div>

    <!-- Fehlermeldungen -->
    <div id="postcode-error" style="color: red; display: none;">Bitte wählen Sie eine gültige PLZ.</div>
    <div id="min-order-warning" style="color: red; display: none;">
      Mindestbestellwert nicht erreicht! Bitte erhöhen Sie Ihren Warenkorb auf mindestens <span id="min-order-amount"><?= $shippingZones[$selectedShippingMethod]['min_order'] ?></span> CHF.
    </div>

    <!-- Submit-Button -->
    <button type="submit" class="checkout-button">Bestellung abschliessen</button>
  </form>
</div>

<?php snippet('footer') ?>

<!-- Checkout-JS -->
<script>
  const taxRate = <?= json_encode($taxRate) ?>;
</script>
<script src="<?= url('assets/js/checkout.js') ?>"></script>
<script>
  // Dynamische Ortsergänzung (unverändert)
  <?php if (!$isPickup): ?>
  document.getElementById('postcode').addEventListener('change', function() {
    const cityInput = document.getElementById('city');
    const postcodeToCity = <?= json_encode($postcodeToCity) ?>;
    cityInput.value = postcodeToCity[this.value] || '';
  });

  // Rechnungsadresse ein-/ausblenden
  document.getElementById('different_billing').addEventListener('change', function() {
    document.getElementById('billing-address-section').style.display =
      this.checked ? 'block' : 'none';
  });
  <?php endif; ?>
</script>

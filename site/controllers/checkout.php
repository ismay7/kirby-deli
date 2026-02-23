<?php
return function() {
  if (!kirby()->request()->is('POST')) {
    return;
  }

  // 1. Pflichtfelder prüfen
  $requiredFields = ['email', 'phone', 'shipping_method'];
  if (get('shipping_method') !== 'pickup') {
    $requiredFields = array_merge($requiredFields, ['delivery_firstname', 'delivery_lastname', 'address', 'postcode']);
  } else {
    $requiredFields = array_merge($requiredFields, ['billing_firstname', 'billing_lastname', 'billing_address', 'billing_postcode', 'billing_city']);
  }

  $errors = [];
  foreach ($requiredFields as $field) {
    if (empty(get($field))) {
      $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' ist ein Pflichtfeld.';
    }
  }

  // 2. E-Mail-Validierung
  if (!filter_var(get('email'), FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
  }

  // 3. Telefon-Validierung
  if (!preg_match('/^\+?[\d\s\-]{8,}$/', get('phone'))) {
    $errors[] = 'Bitte geben Sie eine gültige Telefonnummer ein.';
  }

  // 4. PLZ-Validierung (nur bei Lieferung)
  $shippingZones = [
    'zone1' => ['postcodes' => ['8134', '8041']],
    'zone2' => ['postcodes' => ['8038', '8802']],
    'zone3' => ['postcodes' => ['8136', '8135']],
    'zone4' => ['postcodes' => ['8803', '8800']]
  ];

  if (get('shipping_method') !== 'pickup' && !in_array(get('postcode'), $shippingZones[get('shipping_method')]['postcodes'])) {
    $errors[] = 'Diese PLZ wird in der gewählten Lieferzone nicht bedient.';
  }

  // 5. Mindestbestellwert prüfen
  $cartTotal = 0;
  $cart = json_decode(get('cart'), true) ?? [];
  foreach ($cart as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
  }

  $minOrder = $shippingZones[get('shipping_method')]['min_order'] ?? 0;
  if ($cartTotal < $minOrder) {
    $errors[] = 'Mindestbestellwert nicht erreicht.';
  }

  // 6. Falls Fehler, zurück zum Formular
  if (!empty($errors)) {
    foreach ($errors as $error) {
      echo "<div class='error'>$error</div>";
    }
    return;
  }

};

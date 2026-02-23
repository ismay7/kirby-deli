<?php
// Lieferzonen aus dem Panel auslesen
$shopSettings = site()->find('shop'); // oder der korrekte Seitenname
$shippingZones = [];
$postcodeToCity = [];

if ($shopSettings) {
  foreach ($shopSettings->shippingZones()->toStructure() as $zone) {
    $zoneSlug = strtolower(str_replace(' ', '-', $zone->name()->value()));
    $shippingZones[$zoneSlug] = [
      'name' => $zone->name()->value(),
      'postcodes' => array_map('trim', explode(',', $zone->postcodes()->value())),
      'min_order' => $zone->min_order()->toFloat(),
    ];

    // PLZ → Ort Mapping generieren
    foreach ($zone->postcodeCities()->toStructure() as $pc) {
      $postcodeToCity[trim($pc->postcode()->value())] = $pc->city()->value();
    }
  }
}

// Fallback: Standardzonen, falls keine Panel-Daten vorhanden
if (empty($shippingZones)) {
  $shippingZones = [
    'zone1' => ['name' => 'Zone 1', 'postcodes' => ['8134', '8041'], 'min_order' => 25],
    'zone2' => ['name' => 'Zone 2', 'postcodes' => ['8038', '8802'], 'min_order' => 30],
    'pickup' => ['name' => 'Abholung', 'postcodes' => [], 'min_order' => 0]
  ];
}

return [
  'merx' => [
    'currency' => 'CHF',
    'taxRate' => 2.6,
    'shipping' => [
      'methods' => array_map(function($zoneSlug, $zone) {
        return [
          'name' => $zone['name'],
          'price' => 0,
          'rules' => [
            'cart.total' => ['min' => $zone['min_order']],
            'customer.postcode' => $zone['postcodes'],
          ],
        ];
      }, array_keys($shippingZones), $shippingZones),
    ],
    'payment' => [
      'methods' => [
        'stripe' => [
          'name' => 'Kreditkarte (Stripe)',
          'type' => 'stripe',
          'apiKey' => 'dein_stripe_api_key',
          'test' => [
            'publishable_key' => 'pk_test_xxx…',
            'secret_key' => 'sk_test_xxx…',
          ],
        ],
        'paypal' => [
          'name' => 'PayPal',
          'type' => 'paypal',
          'apiKey' => 'dein_paypal_api_key',
        ],
      ],
    ],
  ],
];

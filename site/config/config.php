<?php

use Kirby\Exception\Exception;

return [
  'debug' => true,
  'panel.vue.compiler' => false,

  // === PAYREXX INTEGRATION ===
  'payrexx.instance' => 'deine-payrexx-instance',  // Test-Instance
  'payrexx.secret'   => 'dein-api-secret',        // Test-Secret

  // 1) Upload hard limit for images (4 MB)
  'hooks' => [
    'file.create:before' => function ($file, $upload) {
      // only check images
      if ($file->type() !== 'image') {
        return;
      }

      $maxBytes = 4 * 1024 * 1024; // 4MB

      if (($upload['size'] ?? 0) > $maxBytes) {
        throw new Exception([
          'message' => 'Bild ist zu gross (max. 4 MB). Bitte vorher komprimieren oder kleiner exportieren.',
        ]);
      }
    },
  ],

  // 2) WebP thumbs presets (frontend output)
  'thumbs' => [
    'presets' => [
      // for menu cards / gallery etc.
      'card' => [
        'width'   => 1400,
        'quality' => 80,
        'format'  => 'webp',
      ],

      // for hero / big backgrounds
      'hero' => [
        'width'   => 2200,
        'quality' => 80,
        'format'  => 'webp',
      ],

      // small thumbs in lists
      'thumb' => [
        'width'   => 600,
        'quality' => 78,
        'format'  => 'webp',
      ],
    ],
  ],

];

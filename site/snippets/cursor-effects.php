<?php
// Prüfe, ob die cursorSettings existieren (unter dem Tab "settings")
if ($site->settings()->cursorSettings()->isNotEmpty()) {
    $enablePizzaCursor = $site->settings()->cursorSettings()->enablePizzaCursor()->toBool();
    $enableCrumbTrail = $site->settings()->cursorSettings()->enableCrumbTrail()->toBool();
    $cursorColor = $site->settings()->cursorSettings()->cursorColor()->value();
    $crumbColor = $site->settings()->cursorSettings()->crumbColor()->value();
} else {
    // Fallback-Werte, falls die Einstellungen nicht geladen werden können
    $enablePizzaCursor = false;
    $enableCrumbTrail = false;
    $cursorColor = '#C5B358';
    $crumbColor = '#C5B358';
}

// Debug-Ausgabe in die Konsole
echo "<script>console.log('Pizza-Cursor: " . ($enablePizzaCursor ? 'aktiviert' : 'deaktiviert') . "', 'Crumb-Trail: " . ($enableCrumbTrail ? 'aktiviert' : 'deaktiviert') . "', 'Cursor-Farbe: " . $cursorColor . "', 'Crumb-Farbe: " . $crumbColor . "');</script>";

// Nur laden, wenn mindestens ein Effekt aktiviert ist
if ($enablePizzaCursor || $enableCrumbTrail):
?>
  <style>
    <?php if ($enableCrumbTrail): ?>
      .cursor-crumb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
      }
      @keyframes crumbFade {
        to { opacity: 0; transform: scale(0.5); }
      }
    <?php endif; ?>
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if ($enablePizzaCursor): ?>
        // Pizza-Cursor aktivieren
        document.body.style.cursor = `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'><circle cx='16' cy='16' r='15' fill='<?= str_replace('#', '%23', $cursorColor) ?>'/><path d='M16 5l-3 5h6zM16 27l-3-5h6zM5 16l5-3v6zM27 16l-5-3v6zM7 7l18 18M7 25l18-18' fill='<?= str_replace('#', '%23', $cursorColor) ?>' stroke='%23853200' stroke-width='1'/></svg>"), auto`;
      <?php endif; ?>

      <?php if ($enableCrumbTrail): ?>
        // Crumb-Trail nur für Geräte mit Maus/Trackpad
        if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
          let lastCrumbTime = 0;

          const handleMove = (e) => {
            const now = performance.now();
            if (now - lastCrumbTime < 30) return;
            lastCrumbTime = now;

            const x = e.clientX;
            const y = e.clientY;

            if (typeof x !== 'number' || typeof y !== 'number') return;

            const crumb = document.createElement('div');
            crumb.classList.add('cursor-crumb');
            crumb.style.width = '8px';
            crumb.style.height = '8px';
            crumb.style.backgroundColor = '<?= $crumbColor ?>';
            crumb.style.left = `${x}px`;
            crumb.style.top = `${y}px`;

            document.body.appendChild(crumb);

            requestAnimationFrame(() => {
              const driftX = (Math.random() - 0.5) * 15;
              const driftY = (Math.random() - 0.5) * 15;
              crumb.style.opacity = '0';
              crumb.style.transform = `translate(${driftX}px, ${driftY}px) scale(0.5)`;
            });

            setTimeout(() => crumb.remove(), 700);
          };

          document.addEventListener('mousemove', handleMove);
          document.addEventListener('pointermove', handleMove);
        }
      <?php endif; ?>
    });
  </script>
<?php endif; ?>

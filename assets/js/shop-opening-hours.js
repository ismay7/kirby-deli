let openingHoursGlobal; // Globale Variable, um openingHours zu speichern

/**
 * Zeigt das Shop-Closed-Popup an.
 */
function showShopClosedPopup() {
  if (document.getElementById('shop-closed-popup')) return;

  const popup = document.createElement('div');
  popup.id = 'shop-closed-popup';
  popup.className = 'popup-overlay';
  popup.innerHTML = `
    <div class="popup-content">
      <h3>Unser Shop ist aktuell geschlossen.</h3>
      <h4>Shop-Öffnungszeiten:</h4>
      <div id="opening-hours-display"></div>
      <button id="popup-ok-button">OK</button>
    </div>
  `;

  // Tage auf Deutsch mappen
  const dayNames = {
    monday: 'Montag',
    tuesday: 'Dienstag',
    wednesday: 'Mittwoch',
    thursday: 'Donnerstag',
    friday: 'Freitag',
    saturday: 'Samstag',
    sunday: 'Sonntag'
  };

  const hoursDisplay = popup.querySelector('#opening-hours-display');
  hoursDisplay.innerHTML = openingHoursGlobal.map(day => {
    if (!day.shifts || day.shifts.length === 0) {
      return `<p><strong>${dayNames[day.day] || day.day}:</strong> geschlossen</p>`;
    } else {
      // Zeitformat ohne Sekunden (z. B. "11:00–14:00")
      const formattedShifts = day.shifts.map(shift => {
        const from = shift.from.split(':').slice(0, 2).join(':'); // "11:00" statt "11:00:00"
        const to = shift.to.split(':').slice(0, 2).join(':');
        return `${from}–${to}`;
      }).join(', ');
      return `<p><strong>${dayNames[day.day] || day.day}:</strong> ${formattedShifts}</p>`;
    }
  }).join('');

  document.body.appendChild(popup);
  document.getElementById('popup-ok-button').onclick = () => {
    document.getElementById('shop-closed-popup').remove();
  };
}

/**
 * Handler für Klicks auf Add-to-Cart- und Checkout-Buttons.
 */
function handleButtonClick(e) {
  e.preventDefault();
  showShopClosedPopup();
}

/**
 * Deaktiviert Add-to-Cart- und Checkout-Buttons und setzt Event-Listener.
 */
function disableShopButtons() {
  // Event-Listener für Add-to-Cart-Buttons (Event Delegation)
  document.removeEventListener('click', globalClickHandler); // Alte Listener entfernen
  document.addEventListener('click', globalClickHandler);

  // Checkout-Button direkt behandeln
  const checkoutButton = document.querySelector('.checkout');
  if (checkoutButton) {
    checkoutButton.disabled = true;
    checkoutButton.onclick = handleButtonClick;
  }

  // Alle Add-to-Cart-Buttons deaktivieren
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.disabled = true;
  });
}

/**
 * Globale Click-Handler-Funktion für Event Delegation
 */
function globalClickHandler(e) {
  if (e.target.classList.contains('add-to-cart')) {
    e.preventDefault();
    showShopClosedPopup();
  }
}

/**
 * Aktiviert Add-to-Cart- und Checkout-Buttons.
 */
function enableShopButtons() {
  document.removeEventListener('click', globalClickHandler); // Event-Listener entfernen

  document.querySelectorAll('.add-to-cart, .checkout').forEach(button => {
    button.disabled = false;
    button.onclick = null;
  });
}

/**
 * Prüft, ob der Shop geöffnet ist.
 */
function isShopOpen(openingHours, shopClosedToggle) {
  if (shopClosedToggle) {
    console.log("Shop ist manuell geschlossen.");
    return false;
  }

  const now = new Date();
  const currentDayGerman = now.toLocaleString('de-CH', { weekday: 'long' });
  const dayMapping = {
    Montag: 'monday',
    Dienstag: 'tuesday',
    Mittwoch: 'wednesday',
    Donnerstag: 'thursday',
    Freitag: 'friday',
    Samstag: 'saturday',
    Sonntag: 'sunday'
  };
  const currentDay = dayMapping[currentDayGerman];
  const currentTime = now.getHours() * 60 + now.getMinutes();

  const todayHours = openingHours.find(day => day.day === currentDay);
  if (!todayHours || !todayHours.shifts || todayHours.shifts.length === 0) {
    console.log(`Keine Öffnungszeiten für ${currentDayGerman} gefunden.`);
    return false;
  }

  const isOpen = todayHours.shifts.some(shift => {
    const [fromHours, fromMinutes] = shift.from.split(':').map(Number);
    const [toHours, toMinutes] = shift.to.split(':').map(Number);
    const fromTime = fromHours * 60 + fromMinutes;
    const toTime = toHours * 60 + toMinutes;
    return currentTime >= fromTime && currentTime <= toTime;
  });

  console.log(`${currentDayGerman} (${currentDay}), ${now.toLocaleTimeString('de-CH')}: Shop ist ${isOpen ? 'geöffnet' : 'geschlossen'}.`);
  return isOpen;
}

/**
 * Initialisiert die Öffnungszeiten-Logik.
 */
function initOpeningHours() {
  const dataElement = document.getElementById('shop-opening-data');
  if (!dataElement) {
    console.error('Element mit Öffnungszeiten-Daten nicht gefunden.');
    return;
  }

  openingHoursGlobal = JSON.parse(dataElement.dataset.openingHours);
  const shopClosedToggle = JSON.parse(dataElement.dataset.shopClosed);

  if (!isShopOpen(openingHoursGlobal, shopClosedToggle)) {
    showShopClosedPopup();
    disableShopButtons();
  } else {
    enableShopButtons();
  }
}

// Initialisierung beim Laden der Seite
document.addEventListener('DOMContentLoaded', initOpeningHours);

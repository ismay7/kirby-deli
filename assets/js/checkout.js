document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('checkout-form');
  const postcodeSelect = document.getElementById('postcode');
  const postcodeError = document.getElementById('postcode-error');
  const minOrderWarning = document.getElementById('min-order-warning');
  const minOrderAmount = parseFloat(document.getElementById('min-order-amount').textContent);
  const taxRate = parseFloat(document.getElementById('tax-rate').value);

  // --- Hilfsfunktion: Fehlermeldung anzeigen/verstecken ---
  function showError(fieldId, message) {
    let errorElement = document.querySelector(`#${fieldId}-error`);
    if (!errorElement) {
      errorElement = document.createElement('div');
      errorElement.className = 'error-message';
      errorElement.id = `${fieldId}-error`;
      document.getElementById(fieldId).after(errorElement);
    }
    errorElement.textContent = message;
  }

  function clearError(fieldId) {
    const errorElement = document.querySelector(`#${fieldId}-error`);
    if (errorElement) errorElement.textContent = '';
  }

  // --- Bestellzusammenfassung aktualisieren ---
  function updateOrderSummary() {
    const orderItemsContainer = document.getElementById('order-items');
    const subtotalElement = document.getElementById('subtotal');
    const taxAmountElement = document.getElementById('tax-amount');
    const orderTotalElement = document.getElementById('order-total');
    const cartTotalElement = document.getElementById('cart-total');

    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let subtotal = 0;
    let orderItemsHTML = '';

    cart.forEach(item => {
      const itemTotal = item.price * item.quantity;
      subtotal += itemTotal;

      orderItemsHTML += `
        <div class="order-item">
          <div class="order-item-name">
            <strong>${item.name} ${item.variant ? `(${item.variant})` : ''}</strong>
            ${item.toppings ? `<div class="order-item-toppings">${item.toppings.map(t => t.name).join(', ')}</div>` : ''}
          </div>
          <div class="order-item-pricing">
            <span>${item.quantity} x ${item.price.toFixed(2)} CHF</span>
            <span class="order-item-total">${itemTotal.toFixed(2)} CHF</span>
          </div>
        </div>
      `;
    });

    orderItemsContainer.innerHTML = orderItemsHTML;

    const taxInfo = subtotal * (taxRate / (1 + taxRate));
    const total = subtotal;

    subtotalElement.textContent = `${subtotal.toFixed(2)} CHF`;
    taxAmountElement.textContent = `${taxInfo.toFixed(2)} CHF`;
    orderTotalElement.textContent = `${total.toFixed(2)} CHF`;
    cartTotalElement.value = total;
  }

  // --- Mindestbestellwert prüfen ---
  function checkMinOrder() {
    const cartTotal = parseFloat(document.getElementById('cart-total').value);
    if (cartTotal < minOrderAmount) {
      minOrderWarning.style.display = 'block';
      return false;
    } else {
      minOrderWarning.style.display = 'none';
      return true;
    }
  }

  // --- Einzelvalidierungen ---
  function validateEmail() {
    const email = document.getElementById('email').value;
    const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    if (!isValid && email) {
      showError('email', 'Bitte eine gültige E-Mail-Adresse eingeben.');
      return false;
    } else {
      clearError('email');
      return true;
    }
  }

  function validatePhone() {
    const phone = document.getElementById('phone').value;
    const isValid = /^\+?[\d\s\-]{8,}$/.test(phone);
    if (!isValid && phone) {
      showError('phone', 'Bitte eine gültige Telefonnummer eingeben (z. B. +41 79 123 45 67).');
      return false;
    } else {
      clearError('phone');
      return true;
    }
  }

  function validatePostcode() {
    if (!postcodeSelect) return true;

    const allowedPostcodes = Array.from(postcodeSelect.options)
      .filter(option => option.value)
      .map(option => option.value);

    if (postcodeSelect.value && !allowedPostcodes.includes(postcodeSelect.value)) {
      postcodeError.style.display = 'block';
      return false;
    } else {
      postcodeError.style.display = 'none';
      return true;
    }
  }

  function validateRequiredFields() {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        showError(field.id, 'Bitte ausfüllen.');
        isValid = false;
      } else {
        clearError(field.id);
      }
    });

    return isValid;
  }

  // --- Event-Listener für Echtzeit-Validierung ---
  document.getElementById('email').addEventListener('blur', validateEmail);
  document.getElementById('phone').addEventListener('blur', validatePhone);
  if (postcodeSelect) postcodeSelect.addEventListener('change', validatePostcode);

  // --- Formular-Validierung beim Absenden ---
  form.addEventListener('submit', function(e) {
    let isValid = true;

    // 1. Pflichtfelder prüfen
    if (!validateRequiredFields()) isValid = false;

    // 2. E-Mail prüfen
    if (!validateEmail()) isValid = false;

    // 3. Telefon prüfen
    if (!validatePhone()) isValid = false;

    // 4. PLZ prüfen
    if (!validatePostcode()) isValid = false;

    // 5. Mindestbestellwert prüfen
    if (!checkMinOrder()) isValid = false;

    if (!isValid) {
      e.preventDefault();
      // Zum ersten Fehler scrollen
      const firstError = form.querySelector('.error-message');
      if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  // --- Initialisierung ---
  updateOrderSummary();
  checkMinOrder();
});

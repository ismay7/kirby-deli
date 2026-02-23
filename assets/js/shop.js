document.addEventListener('DOMContentLoaded', function() {
  // --- Globale Variablen ---
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  const cartItemsContainer = document.querySelector('.cart-items');
  const cartTotalElement = document.querySelector('#cartTotalFull');
  const cartTotalCompact = document.getElementById('cartTotalCompact');
  const cartBadge = document.getElementById('cartBadge');
  const cartHeader = document.getElementById('cartHeader');
  const cartHeaderTitle = document.querySelector('.cart-header-title');
  const checkoutButton = document.querySelector('.checkout');
  const plzErrorElement = document.createElement('div');
  plzErrorElement.id = 'plz-error';
  plzErrorElement.style.color = 'red';
  checkoutButton.after(plzErrorElement);

  let selectedShippingMethod = localStorage.getItem('selectedShippingMethod') || null;
  let minOrderValue = 0;
  const toppingsData = window.toppingsData || [];
  const toppingFactor40cm = window.toppingFactor40cm || 1.25;
  let scrollPosition = 0;

  // --- Abholrabatt-Daten laden ---
  const pickupData = document.getElementById('pickup-data');
  const pickupDiscountValue = pickupData ? parseFloat(pickupData.dataset.pickupDiscountValue) : 0;
  const pickupDiscountType = pickupData ? pickupData.dataset.pickupDiscountType : 'amount';
  let isPickup = selectedShippingMethod === 'pickup';

  // --- Rundungsfunktion für 5 Rappen ---
  function roundToNearestFiveCents(price) {
    return Math.round(price * 20) / 20;
  }

  // --- Preisberechnung mit Abholrabatt (nur Basispreis) ---
  function calculateDiscountedPrice(originalPrice) {
    if (!isPickup) return originalPrice;

    let discountedPrice;
    if (pickupDiscountType === 'amount') {
      discountedPrice = Math.max(0, originalPrice - pickupDiscountValue);
    } else if (pickupDiscountType === 'percent') {
      discountedPrice = originalPrice * (1 - pickupDiscountValue / 100);
    }
    return roundToNearestFiveCents(discountedPrice);
  }

  // --- Preise in Produkt-Buttons aktualisieren ---
  function updateProductPrices() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
      const originalPrice = parseFloat(button.dataset.price);
      const discountedPrice = calculateDiscountedPrice(originalPrice);
      const priceElement = button.querySelector('br').nextSibling;
      priceElement.nodeValue = ` ${discountedPrice.toFixed(2)} CHF`;
    });
  }

  // --- Warenkorb-Höhe anpassen ---
  function adjustCartHeight() {
    const cartElement = document.querySelector('.floating-cart');
    if (cartElement && cartElement.classList.contains('expanded')) {
      cartElement.style.height = 'auto';
      const newHeight = Math.min(cartElement.scrollHeight, window.innerHeight * 0.75);
      cartElement.style.height = `${newHeight}px`;
    }
  }

  // --- Warenkorb-Funktionen ---
  function updateCart() {
    cartItemsContainer.innerHTML = '';
    let total = 0;
    let itemCount = 0;

    cart = cart.filter(item => item.price > 0);

    cart.forEach((item, index) => {
      itemCount += item.quantity;
      const originalPrice = item.originalPrice || item.price;
      const discountedBasePrice = calculateDiscountedPrice(originalPrice);

      // Toppings-Preise unverändert addieren
      const toppingsTotal = item.toppings ? item.toppings.reduce((sum, topping) => sum + topping.price, 0) : 0;
      const itemTotal = (discountedBasePrice + toppingsTotal) * item.quantity;
      total += roundToNearestFiveCents(itemTotal);

      const itemElement = document.createElement('div');
      itemElement.classList.add('cart-item');
      itemElement.dataset.originalPrice = originalPrice;

      const itemNameHTML = `
        <div class="item-name">
          ${item.name} ${item.variant ? `(${item.variant})` : ''}
          ${item.toppings?.length ? `<br><small>${item.toppings.map(t => t.name).join(', ')}</small>` : ''}
        </div>
      `;

      const itemPriceHTML = `
        <div class="price">${itemTotal.toFixed(2)} CHF</div>
      `;

      itemElement.innerHTML = `
        ${itemNameHTML}
        <div class="item-details">
          <div class="quantity-controls">
            <button class="decrease-quantity" data-index="${index}"><i class="ri-subtract-line"></i></button>
            <span class="quantity">${item.quantity}</span>
            <button class="increase-quantity" data-index="${index}"><i class="ri-add-line"></i></button>
          </div>
          ${itemPriceHTML}
          <button class="remove-from-cart" data-index="${index}"><i class="ri-close-line"></i></button>
        </div>
      `;

      cartItemsContainer.appendChild(itemElement);
    });

    // Update beider Total-Anzeigen
    if (cartTotalElement) cartTotalElement.textContent = `Total: ${roundToNearestFiveCents(total).toFixed(2)} CHF`;
    if (cartTotalCompact) cartTotalCompact.textContent = `Total: ${roundToNearestFiveCents(total).toFixed(2)} CHF`;
    if (cartBadge) cartBadge.textContent = itemCount > 0 ? itemCount : '0';

    adjustCartHeight();
    localStorage.setItem('cart', JSON.stringify(cart));
    checkMinOrder();
  }

  function checkMinOrder() {
    const total = cart.reduce((sum, item) => {
      const originalPrice = item.originalPrice || item.price;
      const discountedBasePrice = calculateDiscountedPrice(originalPrice);
      const toppingsTotal = item.toppings ? item.toppings.reduce((sum, topping) => sum + topping.price, 0) : 0;
      const itemTotal = (discountedBasePrice + toppingsTotal) * item.quantity;
      return sum + roundToNearestFiveCents(itemTotal);
    }, 0);

    if (selectedShippingMethod && total < minOrderValue) {
      minOrderWarning.style.display = 'block';
      checkoutButton.disabled = true;
    } else {
      minOrderWarning.style.display = 'none';
      checkoutButton.disabled = false;
    }
    adjustCartHeight();
  }

  // --- Liefermethode-Auswahl ---
  const shippingMethodSelect = document.getElementById('shipping-method');
  const minOrderInfo = document.getElementById('min-order-info');
  const minOrderWarning = document.getElementById('min-order-warning');
  const pickupDiscountInfo = document.getElementById('pickup-discount-info');

  function updatePickupDiscountInfo() {
    if (isPickup) {
      const discountText = pickupDiscountType === 'amount'
        ? `Rabatt für Abholung pro Artikel: ${pickupDiscountValue} CHF`
        : `Rabatt für Abholung pro Artikel: ${pickupDiscountValue}%`;
      pickupDiscountInfo.textContent = discountText;
      pickupDiscountInfo.style.display = 'block';
    } else {
      pickupDiscountInfo.style.display = 'none';
    }
  }

  // --- Liefermethode-Auswahl (Initialisierung) ---
  if (selectedShippingMethod) {
    const option = shippingMethodSelect.querySelector(`option[value="${selectedShippingMethod}"]`);
    if (option) {
      option.selected = true;
      minOrderValue = parseFloat(option.dataset.minOrder) || 0;
      minOrderInfo.style.display = 'block';
      document.getElementById('min-order-amount').textContent = minOrderValue;
      updatePickupDiscountInfo();
    }
  }

  shippingMethodSelect.addEventListener('change', (e) => {
    const option = e.target.selectedOptions[0];
    selectedShippingMethod = option.value;
    isPickup = selectedShippingMethod === 'pickup';
    minOrderValue = parseFloat(option.dataset.minOrder) || 0;
    localStorage.setItem('selectedShippingMethod', selectedShippingMethod);
    minOrderInfo.style.display = 'block';
    document.getElementById('min-order-amount').textContent = minOrderValue;
    plzErrorElement.textContent = '';

    // Preise im Warenkorb aktualisieren
    cart = cart.map(item => {
      const originalPrice = item.originalPrice || item.price;
      const discountedBasePrice = calculateDiscountedPrice(originalPrice);
      const toppingsTotal = item.toppings ? item.toppings.reduce((sum, topping) => sum + topping.price, 0) : 0;
      return { ...item, price: discountedBasePrice + toppingsTotal, originalPrice: originalPrice };
    });

    localStorage.setItem('cart', JSON.stringify(cart));
    updateProductPrices();
    updatePickupDiscountInfo();
    updateCart();
  });

  // --- Kategorien-Tabs ---
  const categoryTabs = document.querySelectorAll('.category-tab');
  const categories = document.querySelectorAll('.category');

  categoryTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      categoryTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const categoryId = tab.dataset.category;
      categories.forEach(c => c.classList.remove('active'));
      document.querySelector(`.category[data-category="${categoryId}"]`).classList.add('active');
    });
  });

  if (categoryTabs.length > 0) {
    categoryTabs[0].classList.add('active');
    document.querySelector('.category').classList.add('active');
  }

  // Toggle für Mobile-Warenkorb
  if (window.innerWidth < 1080 && cartHeader) {
    cartHeader.addEventListener('click', () => {
      const cartElement = document.querySelector('.floating-cart');
      const isExpanded = cartElement.classList.toggle('expanded');

      if (isExpanded) {
        scrollPosition = window.scrollY;
        document.body.classList.add('cart-expanded');
        document.body.style.top = `-${scrollPosition}px`;
        adjustCartHeight();
      } else {
        document.body.classList.remove('cart-expanded');
        window.scrollTo(0, scrollPosition);
        document.body.style.top = '';
        cartElement.style.height = '100px';
      }
    });
  }

  // Event-Listener für Warenkorb-Interaktionen
  cartItemsContainer.addEventListener('click', (e) => {
    if (e.target.closest('.decrease-quantity')) {
      const index = e.target.closest('.decrease-quantity').dataset.index;
      if (cart[index].quantity > 1) cart[index].quantity--;
      else cart.splice(index, 1);
      updateCart();
    }
    if (e.target.closest('.increase-quantity')) {
      const index = e.target.closest('.increase-quantity').dataset.index;
      cart[index].quantity++;
      updateCart();
    }
    if (e.target.closest('.remove-from-cart')) {
      const index = e.target.closest('.remove-from-cart').dataset.index;
      cart.splice(index, 1);
      updateCart();
    }
  });

  // Add-to-Cart-Logik
  document.addEventListener('click', async (e) => {
    if (e.target.closest('.add-to-cart')) {
      const button = e.target.closest('.add-to-cart');
      const productElement = button.closest('.product');
      const hasToppings = productElement.dataset.hasToppings === 'true';
      const productId = button.dataset.productId;
      const variant = button.dataset.variant;
      const productName = productElement.querySelector('h3').textContent.trim();
      let originalPrice = parseFloat(button.dataset.price);

      if (originalPrice <= 0) {
        console.warn(`Ungültiger Preis für ${productName} (${variant}). Abbruch.`);
        return;
      }

      if (hasToppings) {
        e.preventDefault();
        openToppingsPopup(productId, variant, productName, originalPrice);
      } else {
        addToCart({ id: productId, variant, name: productName, originalPrice: originalPrice, price: originalPrice, quantity: 1 });
      }
    }
  });

  // Toppings-Popup
  function openToppingsPopup(productId, variant, productName, basePrice) {
    const popup = document.getElementById('toppings-popup');
    const toppingsList = document.getElementById('toppings-list');
    const selectedSize = variant.includes('32 cm') ? '32 cm' : '40 cm';

    document.body.style.overflow = 'hidden';

    let toppingsWarningElement = document.getElementById('toppings-warning');
    if (!toppingsWarningElement) {
      toppingsWarningElement = document.createElement('div');
      toppingsWarningElement.id = 'toppings-warning';
      toppingsWarningElement.style.color = 'red';
      toppingsWarningElement.style.textAlign = 'right';
      toppingsWarningElement.style.marginTop = '0.5rem';
      toppingsWarningElement.style.display = 'none';
      document.querySelector('.popup-actions').before(toppingsWarningElement);
    }

    toppingsList.innerHTML = toppingsData.map(topping => {
      const price = roundToNearestFiveCents(
        selectedSize === '32 cm'
          ? parseFloat(topping.price32cm)
          : parseFloat(topping.price32cm) * toppingFactor40cm
      );

      return `
        <div class="topping-option">
          <input type="checkbox" id="topping-${topping.name}"
                 data-name="${topping.name}" data-price="${price.toFixed(2)}">
          <label for="topping-${topping.name}">${topping.name} (+${price.toFixed(2)} CHF)</label>
        </div>
      `;
    }).join('');

    popup.style.display = 'flex';

    popup.addEventListener('click', (e) => {
      if (e.target === popup) closeToppingsPopup();
    });

    document.getElementById('confirm-toppings').onclick = () => {
      const selectedToppings = Array.from(document.querySelectorAll('#toppings-list input:checked')).map(el => ({
        name: el.dataset.name,
        price: parseFloat(el.dataset.price)
      }));

      // Prüfe, ob 1-6 Toppings ausgewählt wurden
      if (selectedToppings.length > 6 || selectedToppings.length < 1) {
        toppingsWarningElement.textContent = "Nur 1-6 Zutaten wählbar";
        toppingsWarningElement.style.display = 'block';
        return;
      } else {
        toppingsWarningElement.style.display = 'none';
      }

      const toppingsTotal = selectedToppings.reduce((sum, topping) => sum + topping.price, 0);
      const discountedBasePrice = isPickup ? calculateDiscountedPrice(basePrice) : basePrice;
      const totalPrice = roundToNearestFiveCents(discountedBasePrice + toppingsTotal);

      if (totalPrice > 0) {
        addToCart({
          id: productId,
          variant,
          name: productName,
          originalPrice: basePrice,
          price: totalPrice,
          toppings: selectedToppings,
          quantity: 1
        });
      }
      closeToppingsPopup();
    };

    document.getElementById('cancel-toppings').onclick = closeToppingsPopup;

    function closeToppingsPopup() {
      popup.style.display = 'none';
      document.body.style.overflow = '';
    }
  }

  // Warenkorb-Logik
  function addToCart(item) {
    if (item.price <= 0) {
      console.error("Ungültiges Item:", item);
      return;
    }

    const existingItemIndex = cart.findIndex(cartItem =>
      cartItem.id === item.id &&
      cartItem.variant === item.variant &&
      JSON.stringify(cartItem.toppings) === JSON.stringify(item.toppings)
    );

    if (existingItemIndex >= 0) {
      cart[existingItemIndex].quantity += item.quantity;
    } else {
      cart.push(item);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCart();
  }

  // Checkout-Button
  checkoutButton.addEventListener('click', () => {
    if (!selectedShippingMethod) {
      plzErrorElement.textContent = "Bitte zuerst Liefergebiet oder Abholung wählen.";
      adjustCartHeight();
      return;
    }
    plzErrorElement.textContent = "";
    window.location.href = `/checkout?shipping=${encodeURIComponent(selectedShippingMethod)}`;
  });

  // Initialer Aufruf
  updateProductPrices();
  updateCart();
});


<div id="toppings-popup" class="popup-overlay" style="display: none;">
  <div class="popup-content">
    <h3>Wähle deine Zutaten</h3>
    <p class="popup-subtitle">Basis: Tomatensauce & Mozzarella (inklusive)</p>
    <div id="toppings-list" class="toppings-grid"></div>
    <div class="popup-actions">
      <button id="cancel-toppings" class="button-secondary">Abbrechen</button>
      <button id="confirm-toppings" class="button-primary">Bestätigen</button>
    </div>
  </div>
</div>

<style>
  /* Popup-Overlay */
  #toppings-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(6px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    overflow-y: hidden; /* Verhindert Scrolling im Hintergrund */
  }

  /* Popup-Inhalt */
  #toppings-popup .popup-content {
    background: var(--blue-light);
    padding: 2rem;
    margin: 0 0.5rem;
    border-radius: var(--border-radius);
    max-width: 550px;
    max-height: 80vh;
    box-shadow: var(--boxShadow);
    color: var(--white);
    overflow-y: auto; /* Nur das Popup selbst ist scrollbar */
  }

  .popup-content h3 {
    color: var(--gold);
    margin-top: 0;
    font-family: var(--font-sans);
    text-transform: uppercase;
  }

  .popup-subtitle {
    color: var(--gray);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }

  /* Größenauswahl */
  .pizza-size-selection {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    justify-content: center;
  }

  .pizza-size-selection label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }

  .size-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
    border: 1px solid transparent;
  }

  .pizza-size-selection input[type="radio"]:checked + .size-option {
    border-color: var(--gold);
    background: rgba(255, 255, 255, 0.15);
  }

  /* Toppings-Grid */
  .toppings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    margin: 1.5rem 0;
    max-height: 300px;
    overflow-y: auto;
  }

  .topping-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .topping-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--gold);
  }

  .topping-option label {
    font-family: var(--font-sans);
    font-size: 0.95rem;
    cursor: pointer;
  }

  /* Aktionen */
  .popup-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
  }

  .button-primary {
    background: var(--gold);
    color: var(--blue-mid);
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    font-family: var(--font-sans-bold);
    cursor: pointer;
    transition: background 0.2s;
  }

  .button-primary:hover {
    background: var(--gold-dark);
  }

  .button-secondary {
    background: var(--blue-mid);
    color: var(--white);
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    font-family: var(--font-sans);
    cursor: pointer;
  }
</style>

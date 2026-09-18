// ===== CART =====
let cart = JSON.parse(localStorage.getItem('nayki_cart')) || [];

const cartBtn = document.getElementById('cartBtn');
const cartOverlay = document.getElementById('cartOverlay');
const cartClose = document.getElementById('cartClose');
const cartItemsEl = document.getElementById('cartItems');
const cartTotalEl = document.getElementById('cartTotal');
const cartCountEl = document.getElementById('cartCount');
const checkoutBtn = document.getElementById('checkoutBtn');

function saveCart() {
  localStorage.setItem('nayki_cart', JSON.stringify(cart));
}

function cartItemCount() {
  return cart.reduce((sum, item) => sum + item.qty, 0);
}

function renderCart() {
  cartItemsEl.innerHTML = '';

  if (cart.length === 0) {
    cartItemsEl.innerHTML = '<p class="cart-empty">Wala pang laman ang cart mo.</p>';
  } else {
    cart.forEach((item, index) => {
      const row = document.createElement('div');
      row.className = 'cart-item-row';
      row.innerHTML = `
        <div class="cart-item-info">
          <span class="cart-item-name">${item.name}</span>
          <span class="cart-item-price">₱${item.price.toFixed(2)} each</span>
        </div>
        <div class="cart-item-controls">
          <button class="qty-btn qty-minus" data-index="${index}" aria-label="Bawasan">−</button>
          <span class="qty-value">${item.qty}</span>
          <button class="qty-btn qty-plus" data-index="${index}" aria-label="Dagdagan">+</button>
          <button class="cart-item-remove" data-index="${index}">Cancel</button>
        </div>
      `;
      cartItemsEl.appendChild(row);
    });
  }

  const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
  cartTotalEl.textContent = `₱${total.toFixed(2)}`;
  cartCountEl.textContent = cartItemCount();

  document.querySelectorAll('.qty-plus').forEach(btn => {
    btn.addEventListener('click', () => {
      const i = parseInt(btn.dataset.index, 10);
      cart[i].qty += 1;
      saveCart();
      renderCart();
    });
  });

  document.querySelectorAll('.qty-minus').forEach(btn => {
    btn.addEventListener('click', () => {
      const i = parseInt(btn.dataset.index, 10);
      cart[i].qty -= 1;
      if (cart[i].qty <= 0) cart.splice(i, 1);
      saveCart();
      renderCart();
    });
  });

  document.querySelectorAll('.cart-item-remove').forEach(btn => {
    btn.addEventListener('click', () => {
      const i = parseInt(btn.dataset.index, 10);
      cart.splice(i, 1);
      saveCart();
      renderCart();
    });
  });
}

document.querySelectorAll('.add-cart-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const card = btn.closest('.card');
    const name = card.dataset.name;
    const price = parseFloat(card.dataset.price);

    const existing = cart.find(item => item.name === name);
    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ name, price, qty: 1 });
    }

    saveCart();
    renderCart();

    const originalText = btn.textContent;
    btn.textContent = 'Added ✓';
    btn.classList.add('added');
    setTimeout(() => {
      btn.textContent = originalText;
      btn.classList.remove('added');
    }, 900);
  });
});

cartBtn.addEventListener('click', () => {
  renderCart();
  cartOverlay.classList.add('open');
});

cartClose.addEventListener('click', () => cartOverlay.classList.remove('open'));

cartOverlay.addEventListener('click', (e) => {
  if (e.target === cartOverlay) cartOverlay.classList.remove('open');
});

checkoutBtn.addEventListener('click', () => {
  if (cart.length === 0) {
    alert('Wala ka pang laman sa cart!');
    return;
  }
  alert('Demo checkout lang ito — walang totoong bayad na mangyayari. Salamat sa pag-shop!');
  cart = [];
  saveCart();
  renderCart();
  cartOverlay.classList.remove('open');
});

renderCart();

// ===== LOGIN =====
const loginBtn = document.getElementById('loginBtn');
const loginOverlay = document.getElementById('loginOverlay');
const loginClose = document.getElementById('loginClose');
const loginForm = document.getElementById('loginForm');
const loginTitle = document.getElementById('loginTitle');

let currentUser = JSON.parse(localStorage.getItem('nayki_user')) || null;

function updateLoginButton() {
  loginBtn.textContent = currentUser ? `Logout (${currentUser.email.split('@')[0]})` : 'Login';
}

loginBtn.addEventListener('click', () => {
  if (currentUser) {
    currentUser = null;
    localStorage.removeItem('nayki_user');
    updateLoginButton();
    alert('Na-logout ka na.');
  } else {
    loginOverlay.classList.add('open');
  }
});

loginClose.addEventListener('click', () => loginOverlay.classList.remove('open'));

loginOverlay.addEventListener('click', (e) => {
  if (e.target === loginOverlay) loginOverlay.classList.remove('open');
});

loginForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value;

  if (!email || !password) return;

  currentUser = { email };
  localStorage.setItem('nayki_user', JSON.stringify(currentUser));
  updateLoginButton();
  loginOverlay.classList.remove('open');
  loginForm.reset();
  alert(`Welcome, ${email}!`);
});

updateLoginButton();
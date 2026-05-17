document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('caffe_cart')) || [];

    const cartItemsEl = document.getElementById('cart-items');
    const cartTotalEl = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function saveCart() {
        localStorage.setItem('caffe_cart', JSON.stringify(cart));
    }

    function renderCart() {
        if (!cartItemsEl) return;
        cartItemsEl.innerHTML = '';
        let total = 0;

        if (!cart.length) {
            cartItemsEl.innerHTML = '<p class="text-mid" style="font-size:.95rem;">Shporta është bosh.</p>';
            if (checkoutBtn) checkoutBtn.style.display = 'none';
        } else {
            cart.forEach((item, idx) => {
                total += item.price * item.quantity;
                const row = document.createElement('div');
                row.style.cssText =
                    'display:flex; justify-content:space-between; align-items:center; margin-bottom:.6rem; font-size:.9rem;';
                row.innerHTML =
                    `<span>${item.name} <span style="color:var(--text-mid);">×${item.quantity}</span></span>` +
                    `<span style="display:flex; align-items:center; gap:.5rem;">` +
                    `€${(item.price * item.quantity).toFixed(2)}` +
                    `<button class="remove-btn" data-index="${idx}" style="background:none;border:none;color:#c0392b;cursor:pointer;font-size:1rem;line-height:1;">&times;</button>` +
                    `</span>`;
                cartItemsEl.appendChild(row);
            });
            if (checkoutBtn) checkoutBtn.style.display = 'block';
        }

        if (cartTotalEl) cartTotalEl.textContent = `€${total.toFixed(2)}`;
    }

    // Add to cart
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', e => {
            const id    = btn.dataset.id;
            const name  = btn.dataset.name;
            const price = parseFloat(btn.dataset.price);

            const existing = cart.find(i => i.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }

            saveCart();
            renderCart();

            const orig = btn.textContent;
            btn.textContent = '✓ Shtuar';
            setTimeout(() => (btn.textContent = orig), 1100);
        });
    });

    // Remove from cart
    cartItemsEl?.addEventListener('click', e => {
        if (e.target.classList.contains('remove-btn')) {
            cart.splice(+e.target.dataset.index, 1);
            saveCart();
            renderCart();
        }
    });

    // Checkout
    checkoutBtn?.addEventListener('click', async () => {
        if (!cart.length) return;
        checkoutBtn.textContent = 'Duke procesuar...';
        checkoutBtn.disabled    = true;

        try {
            const res  = await fetch('../api/checkout.php', {
                method:  'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-Token':  csrfToken(),
                },
                body: JSON.stringify({ cart }),
            });
            const data = await res.json();

            if (data.success) {
                cart = [];
                saveCart();
                renderCart();
                if (typeof showToast === 'function')
                    showToast('Porosia u vendos me sukses!', 'success');
                setTimeout(() => { window.location.href = 'user_dashboard.php'; }, 1500);
            } else {
                if (typeof showToast === 'function')
                    showToast(data.error || 'Porosia dështoi.', 'error');
                checkoutBtn.textContent = 'Paguaj Tani';
                checkoutBtn.disabled    = false;
            }
        } catch {
            if (typeof showToast === 'function')
                showToast('Gabim rrjeti. Provoni përsëri.', 'error');
            checkoutBtn.textContent = 'Paguaj Tani';
            checkoutBtn.disabled    = false;
        }
    });

    renderCart();
});

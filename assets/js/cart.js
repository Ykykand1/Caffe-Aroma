document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('caffe_cart')) || [];
    
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    function saveCart() {
        localStorage.setItem('caffe_cart', JSON.stringify(cart));
    }
    
    function renderCart() {
        if (!cartItemsContainer) return;
        
        cartItemsContainer.innerHTML = '';
        let total = 0;
        
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
            if(checkoutBtn) checkoutBtn.style.display = 'none';
        } else {
            cart.forEach((item, index) => {
                total += item.price * item.quantity;
                const div = document.createElement('div');
                div.style.display = 'flex';
                div.style.justifyContent = 'space-between';
                div.style.marginBottom = '0.5rem';
                
                div.innerHTML = `
                    <span>${item.name} (x${item.quantity})</span>
                    <span>$${(item.price * item.quantity).toFixed(2)}
                    <button class="remove-btn" data-index="${index}" style="margin-left:10px; background:none; border:none; color:red; cursor:pointer;">&times;</button>
                    </span>
                `;
                cartItemsContainer.appendChild(div);
            });
            if(checkoutBtn) checkoutBtn.style.display = 'block';
        }
        
        cartTotalElement.innerText = `$${total.toFixed(2)}`;
    }
    
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            const name = e.target.getAttribute('data-name');
            const price = parseFloat(e.target.getAttribute('data-price'));
            
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }
            
            saveCart();
            renderCart();
            
            const originalText = e.target.innerText;
            e.target.innerText = 'Added!';
            setTimeout(() => e.target.innerText = originalText, 1000);
        });
    });
    
    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-btn')) {
                const index = e.target.getAttribute('data-index');
                cart.splice(index, 1);
                saveCart();
                renderCart();
            }
        });
    }
    
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (cart.length === 0) return;
            
            checkoutBtn.innerText = 'Processing...';
            checkoutBtn.disabled = true;
            
            fetch('../api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart: cart })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Order placed successfully!');
                    cart = [];
                    saveCart();
                    window.location.href = 'user_dashboard.php';
                } else {
                    alert(data.error || 'Checkout failed.');
                    checkoutBtn.innerText = 'Checkout';
                    checkoutBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred during checkout.');
                checkoutBtn.innerText = 'Checkout';
                checkoutBtn.disabled = false;
            });
        });
    }
    
    renderCart();
});

document.addEventListener('DOMContentLoaded', () => {
    const btnHot = document.getElementById('btn-hot');
    const btnIced = document.getElementById('btn-iced');
    const coffeeGrid = document.getElementById('coffee-grid');
    const loading = document.getElementById('coffee-loading');

    function fetchCoffees(type) {
        coffeeGrid.innerHTML = '';
        loading.style.display = 'block';

        fetch(`https://api.sampleapis.com/coffee/${type}`)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                renderCoffees(data);
            })
            .catch(error => {
                loading.style.display = 'none';
                coffeeGrid.innerHTML = `<p class="alert alert-error">Error loading coffees: ${error.message}</p>`;
            });
    }

    function renderCoffees(coffees) {
        coffees.forEach(coffee => {
            const div = document.createElement('div');
            div.className = 'product-card';
            
            const ingredients = coffee.ingredients ? coffee.ingredients.join(', ') : 'Unknown';
            const imgUrl = coffee.image || 'https://images.unsplash.com/photo-1511920170033-f8396924c348?w=500&q=80';
            
            div.innerHTML = `
                <img src="${imgUrl}" alt="${coffee.title}" class="product-image" onerror="this.src='https://images.unsplash.com/photo-1511920170033-f8396924c348?w=500&q=80'">
                <div class="product-info">
                    <h3>${coffee.title}</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">${coffee.description}</p>
                    <p style="font-size: 0.85rem; font-weight: 500; margin-top: auto;"><strong>Ingredients:</strong> ${ingredients}</p>
                </div>
            `;
            coffeeGrid.appendChild(div);
        });
    }

    btnHot.addEventListener('click', () => {
        btnHot.className = 'btn';
        btnIced.className = 'btn btn-secondary';
        fetchCoffees('hot');
    });

    btnIced.addEventListener('click', () => {
        btnIced.className = 'btn';
        btnHot.className = 'btn btn-secondary';
        fetchCoffees('iced');
    });

    fetchCoffees('hot');
});

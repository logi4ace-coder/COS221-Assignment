document.addEventListener('DOMContentLoaded', async () => {
    const wishlistIds = JSON.parse(localStorage.getItem('wishlist')) || [];
    const container = document.getElementById('wishlist-items');
    const emptyMessage = document.getElementById('empty-wishlist');

    if (wishlistIds.length === 0)
    {
        emptyMessage.style.display = 'block';
        return;
    }

    try
    {
        const response = await fetch('https://fakestoreapi.com/products');
        const allProducts = await response.json();
        const wishlistProducts = allProducts.filter(product =>
            wishlistIds.includes(product.id)
        );

        container.innerHTML = '';

        wishlistProducts.forEach(product => {
            const item = document.createElement('div');
            item.className = 'wishlist-item';
            item.innerHTML = `
                <img src="${product.image}" alt="${product.title}">
                <div class="item-info">
                    <h3>${product.title}</h3>
                    <div class="price">$${product.price}</div>
                    <button class="remove-btn" data-id="${product.id}">Remove</button>
                </div>
            `;
            container.appendChild(item);
        });


        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', removeFromWishlist);
        });

    }
    catch (error)
    {
        console.error('Error loading wishlist:', error);
        container.innerHTML = '<p>Error loading your wishlist. Please try again.</p>';
    }
});

function removeFromWishlist(event)
{
    const productId = parseInt(event.target.dataset.id);
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    wishlist = wishlist.filter(id => id !== productId);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));

    event.target.closest('.wishlist-item').remove();

    if (wishlist.length === 0)
    {
        document.getElementById('empty-wishlist').style.display = 'block';
    }
}

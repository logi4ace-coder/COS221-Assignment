document.addEventListener('DOMContentLoaded', async () => {
  const apiKey = localStorage.getItem('api_key');
  const container = document.getElementById('wishlist-items');
  const emptyMessage = document.getElementById('empty-wishlist');

  if (!apiKey) {
    emptyMessage.innerHTML = "You must be logged in to view your wishlist.";
    emptyMessage.style.display = 'block';
    return;
  }

  try {
    const response = await fetch('/Assignment/api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        type: "wishlistDisplay",
        fill: "true",
        api_key: apiKey
      })
    });

    const result = await response.json();

    if (result.status !== 'success' || !Array.isArray(result.data)) {
      throw new Error(result.message || "Invalid response");
    }

    const wishlistProducts = result.data;

    if (wishlistProducts.length === 0) {
      emptyMessage.style.display = 'block';
      return;
    }

    container.innerHTML = '';

    wishlistProducts.forEach(product => {
      const item = document.createElement('div');
      item.className = 'wishlist-item';
      item.innerHTML = `
        <img src="${product.image_url}" alt="${product.title}">
        <div class="item-info">
          <h3>${product.title}</h3>
          <div class="price">R${product.final_price}</div>
          <button class="remove-btn" data-id="${product.id}">Remove</button>
        </div>
      `;
      container.appendChild(item);


      const removeBtn = item.querySelector('.remove-btn');
      removeBtn.addEventListener('click', async () => {
        const confirmRemoval = confirm("Remove this item from your wishlist?");
        if (!confirmRemoval) return;

        try {
          const res = await fetch('/Assignment/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              type: "wishlistDel",
              product_id: product.id,
              api_key: apiKey
            })
          });

          const delResult = await res.json();

          if (delResult.status === 'success') {
            item.remove();
            if (!container.querySelector('.wishlist-item')) {
              emptyMessage.style.display = 'block';
            }
          } else {
            alert('❌ Failed to remove item: ' + (delResult.message || 'Unknown error'));
          }
        } catch (err) {
          console.error("Remove failed", err);
          alert('❌ Error removing item. Try again.');
        }
      });
    });

  } catch (error) {
    console.error('Error loading wishlist:', error);
    container.innerHTML = '<p>Error loading your wishlist. Please try again.</p>';
  }
});


function removeFromWishlist(productId, wishlistItemElement) {
  const apiKey = localStorage.getItem('api_key');

  if (!apiKey) {
    alert("You're not logged in.");
    return;
  }

  fetch('/Assignment/api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify({
      type: "wishlistDel",
      product_id: productId,
      api_key: apiKey
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      wishlistItemElement.remove(); 
      alert('✅ Successfully removed item from wishlist');
    } else {
      console.error(data.message);
      alert('❌ Failed to remove item: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error during removal:', error);
    alert('❌ Error connecting to server');
  });
}

function getProductIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return parseInt(params.get('product'));
}

document.addEventListener("DOMContentLoaded", function () {
    async function fetchProductById(productId) {
        try {
            const response = await fetch('/Assignment/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: productId, type: 'getProductByID' })
            });

            const result = await response.json();
            if (result.status === 'success') {
                displayProduct(result.data[0]);
            } else {
                console.error(result.message);
            }
        } catch (error) {
            console.error("Fetch error:", error);
        }
    }

    const productId = getProductIdFromURL();
    if (!isNaN(productId)) {
        fetchProductById(productId);
    } else {
        console.error("Invalid product ID in URL");
    }
});

function displayProduct(product) {
    const container = document.getElementById('product-container');
    container.innerHTML = ''; 
    const productHTML = `
        <div style="border: 1px solid #ccc; padding: 20px; max-width: 800px;">
            <h2>${product.Name}</h2>
            <img src="${product.ImageURL}" alt="${product.Name}" style="max-width: 300px; height: auto;">
            <p><strong>Brand:</strong> ${product.Brand}</p>
            <p><strong>Material:</strong> ${product.Material}</p>
            <p><strong>Description:</strong> ${product.Description}</p>
            <p><strong>Rating:</strong> ${product.Rating}</p>
            <p><strong>Color:</strong> ${product.Color}</p>

            <h3>Listings</h3>
            <ul>
                ${product.Listings.map(listing => `
                    <li style="margin-bottom: 10px;">
                        <p><strong>Size:</strong> ${listing.Size}</p>
                        <p><strong>Stock:</strong> ${listing.Stock}</p>
                        <p><strong>Price:</strong> ${listing.Price} ${listing.Currency}</p>
                        <p><strong>Retailer:</strong> ${listing.Retailer.Name} (${listing.Retailer.Type}, ${listing.Retailer.Country})</p>
                        <p><a href="under_construction.php" target="_blank">Retailer Website</a></p>
                        <p><strong>Store Address:</strong> ${listing.Retailer.StoreAddress}</p>
                        <p><strong>Active:</strong> ${listing.Retailer.IsActive ? 'Yes' : 'No'}</p>
                    </li>
                `).join('')}
            </ul>

            <h3>Reviews</h3>
            <div id="reviews-section">
                <div id="loader" style="display: none; justify-content: center;"><p>Loading reviews...</p></div>
                <div id="reviews-content"></div>
            </div>
        </div>
    `;

    container.innerHTML = productHTML;


    fetchAndDisplayReviews(product.ProductID);
}

function fetchAndDisplayReviews(productId) {
    const reviewsContainer = document.getElementById('reviews-content');
    showLoader();

    fetch('/Assignment/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            type: 'handleGetReviews',
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoader();
        if (data.status === 'success' && data.reviews.length > 0) {
            reviewsContainer.innerHTML = data.reviews.map(review => `
                <div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9; border-radius: 8px;">
                    <div style="margin-bottom: 5px;">${getStarSVGs(review.rating)}</div>
                    <p style="margin: 5px 0;"><strong>${review.reviewer}</strong> on <em>${review.date}</em></p>
                    <p style="margin: 5px 0;">${review.text}</p>
                </div>
            `).join('');
        } else {
            reviewsContainer.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <svg width="64" height="64" fill="none" stroke="#ccc" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h.01M4.93 4.93a10 10 0 0114.14 0M9 9l6 6" />
                    </svg>
                    <p style="color: #999;">No reviews yet. Be the first to review!</p>
                </div>
            `;
        }
    })
    .catch(error => {
        hideLoader();
        reviewsContainer.innerHTML = `<p style="color: red;">Error loading reviews.</p>`;
        console.error("Error fetching reviews:", error);
    });
}

function createReview(reviewData) {
    fetch('/Assignment/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            type: 'handleCreateReview',
            ...reviewData
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            displayReview(result.data);
        } else {
            console.error('Review creation failed:', result.message);
        }
    })
    .catch(error => {
        console.error('Network or server error:', error);
    });
}

function displayReview(review) {
    const container = document.createElement('div');
    container.classList.add('review');

    container.innerHTML = `
        <div class="review-header">
            <strong>${review.reviewer_name}</strong>
            <span class="review-date">${review.review_date}</span>
        </div>
        <div class="review-rating">${getStarSVGs(review.rating)}</div>
        <p class="review-text">${escapeHTML(review.comment || review.review_text)}</p>
    `;

    const reviewsContainer = document.getElementById('reviews-content');
    if (reviewsContainer) {
        reviewsContainer.prepend(container);
    } else {
        console.error("reviews-content container not found.");
    }
}


function escapeHTML(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


function getStarSVG(filled) {
    return `
        <svg width="24" height="24" fill="${filled ? '#f59e0b' : 'none'}" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="margin-right: 4px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
        </svg>
    `;
}

function getStarSVGs(rating) {
    let svg = '';
    for (let i = 1; i <= 5; i++) {
        svg += i <= rating ? getStarSVG(true) : getStarSVG(false);
    }
    return svg;
}


document.getElementById('review-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const apiKey = localStorage.getItem('api_key');
    if (!apiKey) {
        console.error('API key missing from local storage.');
        return;
    }

    const now = new Date().toISOString().split('T')[0];

    const reviewData = {
        api_key: apiKey,
product_id: getProductIdFromURL(),
        rating: parseInt(document.getElementById('rating').value),
        review_text: document.getElementById('review_text').value,
        review_date: now
    };

    createReview(reviewData);

    this.reset();
});

function renderStars(containerId, hiddenInputId) {
    const container = document.getElementById(containerId);
    const hiddenInput = document.getElementById(hiddenInputId);

    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('span');
        star.innerHTML = '☆'; 
        star.dataset.value = i;
        star.style.fontSize = '24px';

        star.addEventListener('click', function () {
            hiddenInput.value = i;
            highlightStars(container, i);
        });

        container.appendChild(star);
    }
}

function highlightStars(container, rating) {
    const stars = container.querySelectorAll('span');
    stars.forEach(star => {
        star.innerHTML = parseInt(star.dataset.value) <= rating ? '★' : '☆';
    });
}


renderStars('star-rating', 'rating');
function showLoader() {
  document.getElementById("loader").style.display = "flex";
}

function hideLoader() {
  document.getElementById("loader").style.display = "none";
}
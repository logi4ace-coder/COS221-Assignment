

    document.getElementById('theme-Dark').addEventListener('click', () => {
    document.body.classList.add('dark-theme');
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
});

    document.getElementById('theme-Light').addEventListener('click', () => {
    document.body.classList.remove('dark-theme');
    document.documentElement.removeAttribute('data-theme');
    localStorage.setItem('theme', 'light');
});


    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
    document.getElementById('theme-Dark').click();
}

    function changeMainImage(src)
    {
        document.getElementById('main-image').src = src;
    }


    const retailers = [
    {
        name: "Amazon",
        logo: "https://logo.clearbit.com/amazon.com",
        price: "$109.95",
        url: "https://www.amazon.com"
    },
    {
        name: "Walmart",
        logo: "https://logo.clearbit.com/walmart.com",
        price: "$112.99",
        url: "https://www.walmart.com"
    },
    {
        name: "Best Buy",
        logo: "https://logo.clearbit.com/bestbuy.com",
        price: "$115.49",
        url: "https://www.bestbuy.com"
    },
    {
        name: "Target",
        logo: "https://logo.clearbit.com/target.com",
        price: "$110.25",
        url: "https://www.target.com"
    }
    ];


    const retailerList = document.getElementById('retailer-list');
    retailers.forEach(retailer => {
    const retailerCard = document.createElement('div');
    retailerCard.className = 'retailer-card';
    retailerCard.innerHTML = `
                <div class="retailer-info">
                    <img src="${retailer.logo}" alt="${retailer.name}" class="retailer-logo">
                    <span class="retailer-name">${retailer.name}</span>
                </div>
                <div class="retailer-price">${retailer.price}</div>
                <a href="${retailer.url}" target="_blank" class="buy-button">Get Now</a>
            `;
    retailerList.appendChild(retailerCard);
});


    const themeDark = document.getElementById('theme-Dark');
    const themeLight = document.getElementById('theme-Light');

    themeDark.addEventListener('click', () => {
    document.documentElement.setAttribute('data-theme', 'dark');
});

    themeLight.addEventListener('click', () => {
    document.documentElement.removeAttribute('data-theme');
});


    const addToWishlistBtn = document.querySelector('.add-to-wishlist');
    addToWishlistBtn.addEventListener('click', () => {

    addToWishlistBtn.textContent = 'Added to Wishlist';
    addToWishlistBtn.style.backgroundColor = '#42ff33';
    addToWishlistBtn.style.color = 'white';
    setTimeout(() => {
    addToWishlistBtn.textContent = 'Add to Wishlist';
    addToWishlistBtn.style.backgroundColor = '';
    addToWishlistBtn.style.color = '';
}, 2000);
});

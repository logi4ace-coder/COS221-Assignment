let currentlySearching = false;
let allProducts = [];

document.addEventListener("DOMContentLoaded", () => {
  // Load products
  fetchAllProducts().then(data => {
    allProducts = data;
    console.log("Loaded products:", allProducts);
    renderCustomerProducts(allProducts);
  });

  // Search button click
  const searchBtn = document.getElementById("search-btn");
  if (searchBtn) {
    searchBtn.addEventListener("click", searchProducts);
  }

  // Search input enter key
  const searchInput = document.getElementById("search-input");
  if (searchInput) {
    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        searchProducts();
      }
    });
  }

  // Filters
  const priceSlider = document.querySelector('input[type="range"]');
  const priceMinInput = document.querySelector('.price-inputs input[placeholder="Min"]');
  const priceMaxInput = document.querySelector('.price-inputs input[placeholder="Max"]');
  const sizeCheckboxes = document.querySelectorAll('input[id^="size-"]');
  const stockCheckboxes = document.querySelectorAll('input[id$="stock"]');
  const currencyRadios = document.querySelectorAll('input[name="currency"]');
  const shopCheckboxes = document.querySelectorAll('input[id^="shop-"]');
  const materialCheckboxes = document.querySelectorAll('input[id^="material-"]');

  const filterInputs = [
    priceSlider,
    priceMinInput,
    priceMaxInput,
    ...sizeCheckboxes,
    ...stockCheckboxes,
    ...currencyRadios,
    ...shopCheckboxes,
    ...materialCheckboxes
  ];

  filterInputs.forEach(input => {
    input.addEventListener('input', applyFilters);
  });
});

function fetchAllProducts() {
  const loader = document.getElementById("loader");
  loader.style.display = "flex";

  return fetch("/Assignment/api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ type: "GetAllProducts" })
  })
    .then(response => response.json())
    .then(response => {
      loader.style.display = "none";
      if (response.status === "success" && Array.isArray(response.data)) {
        return response.data;
      } else {
        console.error("Unexpected data format:", response);
        return [];
      }
    })
    .catch(error => {
      loader.style.display = "none";
      console.error("Error fetching products:", error);
      return [];
    });
}

function renderCustomerProducts(products) {
  const container = document.querySelector(".pro-show");
  container.innerHTML = "";

  products.forEach(product => {
    const card = document.createElement("div");
    card.classList.add("product-card");

    const productId = product.ProductID;
    const imageUrl = product.ImageURL || "https://via.placeholder.com/250x180";
    const name = product.Name;
    const ratingStars = "★".repeat(product.Rating) + "☆".repeat(5 - product.Rating);

    card.innerHTML = `
      <a href="view.php?product=${productId}">
        <img src="${imageUrl}" alt="${name}" class="prod-img" />
      </a>
      <div class="pro-info">
        <p class="pro-name">${name}</p>
        <div class="rating">${ratingStars}</div>
        <button class="btn btn-primary compare-btn" onclick="comparePrices(${productId})">Compare Prices</button>
      </div>
    `;

    container.appendChild(card);
  });
}

function searchProducts() {
  if (currentlySearching) return;
  currentlySearching = true;

  const searchTerm = document.getElementById('search-input').value.toLowerCase();
  const loadingGif = document.getElementById('loader');
  const noResultsGif = document.getElementById('no-results-gif');
  const noResultsDiv = document.getElementById('no-results-div');
  const productsSection = document.querySelector('.pro-show');

  loadingGif.style.display = 'block';
  noResultsGif.style.display = 'none';
  noResultsDiv.style.display = 'none';
  productsSection.innerHTML = '';

  const filteredProducts = allProducts.filter(product => {
    const name = product.Name?.toLowerCase() || '';
    const desc = product.Description?.toLowerCase() || '';
    return name.includes(searchTerm) || desc.includes(searchTerm);
  });

  loadingGif.style.display = 'none';

  if (filteredProducts.length > 0) {
    renderCustomerProducts(filteredProducts);
  } else {
    showNoResultDivs();
  }

  currentlySearching = false;
}

function showNoResultDivs() {
  console.log("No results logic is being executed.");
  document.body.style.overflow = 'hidden';
  document.body.style.height = '100vh';
  document.getElementById('no-results-gif').style.display = 'block';
  document.getElementById('no-results-div').style.display = 'block';
}

function goBackHome() {
  window.location.href = 'index.php';
}

function applyFilters() {
  const priceSlider = document.querySelector('input[type="range"]');
  const priceMinInput = document.querySelector('.price-inputs input[placeholder="Min"]');
  const priceMaxInput = document.querySelector('.price-inputs input[placeholder="Max"]');

  const priceMin = parseFloat(priceMinInput.value) || 0;
  const priceMax = parseFloat(priceMaxInput.value) || parseFloat(priceSlider.max);

  const selectedSizes = Array.from(document.querySelectorAll('input[id^="size-"]:checked')).map(cb => cb.id.replace('size-', ''));
  const selectedStock = Array.from(document.querySelectorAll('input[id$="stock"]:checked')).map(cb => cb.value);
  const selectedCurrency = Array.from(document.querySelectorAll('input[name="currency"]:checked')).map(cb => cb.value);
  const selectedShops = Array.from(document.querySelectorAll('input[id^="shop-"]:checked')).map(cb => cb.value);
  const selectedMaterials = Array.from(document.querySelectorAll('input[id^="material-"]:checked')).map(cb => cb.value);

  const filtered = allProducts.filter(product => {
    const hasValidListing = product.Listings.some(listing => {
      const price = parseFloat(listing.Price);
      return (
        (!selectedSizes.length || selectedSizes.includes(listing.Size)) &&
        (!selectedStock.length || selectedStock.includes(listing.Stock)) &&
        (!selectedCurrency.length || selectedCurrency.includes(listing.Currency)) &&
        (!selectedShops.length || selectedShops.includes(listing.Retailer.Name)) &&
        (!priceMin || price >= priceMin) &&
        (!priceMax || price <= priceMax)
      );
    });

    return (
      (!selectedMaterials.length || selectedMaterials.includes(product.Material)) &&
      hasValidListing
    );
  });

  renderCustomerProducts(filtered);
}

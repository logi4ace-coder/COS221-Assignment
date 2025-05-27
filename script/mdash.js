let currentChart = null;
document.addEventListener("DOMContentLoaded", () => {
  const apiKey = localStorage.getItem("api_key");

  if (!apiKey) {
    document.getElementById("productList").innerHTML = "<p>API key not found. Please log in.</p>";
    return;
  }

  initUserDetails();
  showSection("productManagement");
  fetchAndRenderProducts(apiKey);
  fetchAndRenderPriceAlerts(apiKey);
  fetchAndRenderAnalytics(apiKey);
  bindFormListeners(apiKey);
  updateBuss();
});
function fetchAndRenderProducts(apiKey) {
  const container = document.getElementById("productList");
  showLoader();
  container.innerHTML = "";

  fetch('/Assignment/api.php', {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      type: "getAllProductsManager",
      api_key: apiKey
    })
  })
  .then(res => res.json())
  .then(data => {
    hideLoader();
console.log(data);
console.log("Data received:", data);
console.log("Is Array:", Array.isArray(data.data));
console.log("Length:", data.data?.length);

    if (data.status !== "success" || !data.data.length) {
      container.innerHTML = `
        <div style="text-align:center; padding: 40px; color: #666;">
          <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="none" stroke="#999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box" viewBox="0 0 24 24">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4.18a2 2 0 0 0-2 0L3 6.27A2 2 0 0 0 2 8v8a2 2 0 0 0 1 1.73l7 4.18a2 2 0 0 0 2 0l7-4.18A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
          </svg>
          <p style="margin-top: 15px; font-size: 18px;">No products found. Add Products To Jump Start Your Business!</p>
        </div>
      `;
      return;
    }

    container.innerHTML = data.data.map(product => `
  <div class="product-card" data-product-id="${product.ProductID}">
    <img src="${product.ImageURL || 'default.jpg'}" alt="${product.Name}" style="max-width: 150px;" />
    <h3>${product.Name}</h3>
    <p><strong>Brand:</strong> ${product.Brand}</p>
    <p><strong>Material:</strong> ${product.Material || 'N/A'}</p>
    <p><strong>Description:</strong> ${product.Description}</p>
    <p><strong>Rating:</strong> ${product.Rating || 'N/A'}</p>
    <p><strong>Color:</strong> ${product.Color || 'N/A'}</p>
    <p><strong>Price:</strong> $${(product.Price && !isNaN(product.Price)) ? Number(product.Price).toFixed(2) : 'N/A'}</p>
    <p><strong>Stock:</strong> ${product.Stock}</p>
    <button class="remove-btn" data-product-id="${product.ProductID}">Remove Product</button>
  </div>
`).join("");



    document.querySelectorAll('.remove-btn').forEach(button => {
      button.addEventListener('click', () => removeProduct(button.dataset.productId, apiKey));
    });
  })
  .catch(() => {
    hideLoader();
    container.innerHTML = `<p style="color:red; text-align:center;">Failed to fetch products. Please try again.</p>`;
  });
}


function initUserDetails() {
  document.getElementById("managerEmail").textContent = localStorage.getItem("user_email") || "N/A";
  document.getElementById("managerRole").textContent = localStorage.getItem("user_role") || "N/A";
}

function showLoader() {
  document.getElementById("loader").style.display = "flex";
}

function hideLoader() {
  document.getElementById("loader").style.display = "none";
}

function fetchAndRenderAnalytics(apiKey) {

  
  fetch('/Assignment/api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      type: 'GetClickAnalytics',
      api_key: apiKey
    })
  })
  .then(res => {
    console.log("Fetch response received, status:", res.status);
    return res.json();
  })
  .then(data => {
    console.log("Parsed JSON data:", data);
    const chartContainer = document.getElementById("analytics");
    chartContainer.style.display = 'block';

    if (window.currentChart) {
      window.currentChart.destroy();
      window.currentChart = null;
    }

    if (
      data.status !== 'success' ||
      !Array.isArray(data.data) ||
      data.data.length === 0 ||
      data.data.every(v => v === 0 || v === null)
    ) {
      chartContainer.innerHTML = `
        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px; color:#aaa;">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20v-6M6 20v-4M18 20v-10M3 20h18" />
            <circle cx="12" cy="4" r="2" fill="#2ecc71"/>
          </svg>
          <h3 style="margin-top:20px; color:#2ecc71;">No Clicks Yet</h3>
          <p style="color:#999; font-style:italic;">No customer clicks just yet. Keep an eye out — your dashboard will update as soon as they start exploring!</p>
        </div>
      `;
      return;
    }

    chartContainer.innerHTML = `
      <h2>Business Analytics</h2>
      <canvas id="clicksChart" width="600" height="300"></canvas>
    `;

    const canvas = document.getElementById("clicksChart");
    if (!canvas) {
      console.error("Canvas element not found after innerHTML update");
      return;
    }

    const ctx = canvas.getContext("2d");
    window.currentChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Clicks Over Time',
          data: data.data,
          borderColor: 'rgba(46, 204, 113, 1)',
          fill: false,
          tension: 0.4
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  })
  .catch(err => {
    console.error("Error fetching analytics:", err);
  });
}




function removeProduct(productId, apiKey) {
  if (!confirm("Are you sure you want to remove this product?")) return;

  fetch('/Assignment/api.php', {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      type: "removeProduct",
      api_key: apiKey,
      productId: productId
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Product removed successfully.");
      fetchAndRenderProducts(apiKey);
    } else {
      alert("Failed to remove product: " + (data.message || "Unknown error"));
    }
  });
}


function fetchAndRenderPriceAlerts(apiKey) {
  const alertsList = document.getElementById("alertsList");
  const alertsSection = document.getElementById("priceAlerts");
  const notifyButton = document.getElementById("notify");

  alertsList.innerHTML = "";
  alertsSection.style.display = "none";
  notifyButton.style.display = "none";
  showLoader();

  fetch('/Assignment/api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      type: 'getManagerPriceAlerts',
      api_key: apiKey 
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status !== "success" || !Array.isArray(data.data) || !data.data.length) {

      alertsSection.style.display = "block";
     alertsList.innerHTML = `
  <div style="
    display: flex; 
    align-items: center; 
    gap: 12px; 
    padding: 14px 18px; 
    background: linear-gradient(135deg, #e9f9f1, #d6f4e3); 
    border-radius: 12px; 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); 
    font-weight: 600; 
    font-family: 'Segoe UI', sans-serif; 
    color: #27ae60;
    animation: fadeInUp 0.4s ease-out;
  ">
    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#27ae60" viewBox="0 0 24 24">
      <path d="M12 0C5.371 0 0 5.371 0 12c0 6.627 5.371 12 12 12s12-5.373 12-12c0-6.629-5.371-12-12-12zm-1.2 17.4l-5.2-5.2 1.6-1.6 3.6 3.6 7.2-7.2 1.6 1.6-8.8 8.8z"/>
    </svg>
    <span> No price alerts at the moment.</span>
  </div>

  <style>
    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(10px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
`;

      return;
    }


    alertsSection.style.display = "block";
    notifyButton.style.display = "inline-block"; 

    alertsList.innerHTML = data.data.map(alert => `
      <div class="alert-card" data-product-id="${alert.ProductID}">
        <p><strong>Product:</strong> ${alert.ProductName}</p>
        <input type="number" class="new-price-input" placeholder="New price" />
        <button class="notify-btn" data-product-id="${alert.ProductID}">Notify</button>
      </div>
    `).join('');

    document.querySelectorAll('.notify-btn').forEach(button => {
      button.addEventListener('click', () => handleNotify(button, apiKey));
    });
  })
  .catch(err => {
    console.error("Error fetching alerts:", err);
  })
  .finally(() => hideLoader());
}


function bindFormListeners(apiKey) {
  const form = document.getElementById("productForm");
  
  form.addEventListener("submit", function (e) {
    e.preventDefault(); 

    form.querySelector("button[type='submit']").disabled = true;

    const formData = new FormData(form);
    const productData = {};

    formData.forEach((value, key) => {
      productData[key] = value;
    });

    fetch('/Assignment/api.php', {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        type: "addProduct",
        api_key: apiKey,
        product: productData
      })
    })
    .then(res => res.json())
    .then(data => {
      alert(data.message || "Product added.");
      fetchAndRenderProducts(apiKey);
      form.reset();
    })
    .finally(() => {
      form.querySelector("button[type='submit']").disabled = false;
    });
  }, { once: true });
}

function handleNotify(button, apiKey) {
  const productId = button.getAttribute("data-product-id");
  const parent = button.closest(".alert-card");
  const newPrice = parseFloat(parent.querySelector(".new-price-input").value);

  if (!newPrice || newPrice <= 0) return alert("Invalid price");

  fetch("/Assignment/api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      type: "notifyPriceAlerts",
      api_key: apiKey,
      productId,
      newPrice
    })
  })
    .then(res => res.json())
    .then(result => {
      alert(result.message || "Notification failed");
      if (result.status === 'success') parent.querySelector(".new-price-input").value = '';
    });
}

function updateBuss() {
  const apiKey = localStorage.getItem('api_key');


  fetch('/Assignment/api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ type: 'fetchProfileInfo', api_key: apiKey })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('managerEmail').textContent = data.email;

        
        const nameEl = document.getElementById('businessName');
        nameEl.textContent = data.businessName;
        nameEl.classList.add('flash-success');
        setTimeout(() => nameEl.classList.remove('flash-success'), 1500);

        document.getElementById('profileInfo').style.display = 'block';
      } else {
        alert(data.error);
      }
    });


    document.getElementById('businessForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const businessName = document.getElementById('newBusinessName').value;
    const messageBox = document.getElementById('messageBox');
    messageBox.style.display = 'block';

    const animatedErrorSVG = `
  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
    <style>
      @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-3px); }
        50% { transform: translateX(3px); }
        75% { transform: translateX(-3px); }
        100% { transform: translateX(0); }
      }
    </style>
    <g style="animation: shake 0.5s ease-in-out 3;"> <!-- only shakes 3 times -->
      <circle cx="12" cy="12" r="10" />
      <line x1="15" y1="9" x2="9" y2="15" />
      <line x1="9" y1="9" x2="15" y2="15" />
    </g>
  </svg>
`;

const animatedCheckmarkSVG = `
  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
    <style>
      @keyframes dashCircle {
        to { stroke-dashoffset: 0; }
      }
      @keyframes dashCheck {
        to { stroke-dashoffset: 0; }
      }
    </style>
    <circle
      cx="12"
      cy="12"
      r="10"
      style="stroke-dasharray: 62.8; stroke-dashoffset: 62.8; animation: dashCircle 1s forwards;"
    />
    <path
      d="M9 12l2 2 4-4"
      style="stroke-dasharray: 12; stroke-dashoffset: 12; animation: dashCheck 0.5s 1s forwards;"
    />
  </svg>
`;


    fetch('/Assignment/api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
body: JSON.stringify({ type: 'updateBusinessName', api_key: apiKey, businessName })
    })
      .then(res => res.json())
      .then(data => {
        const nameEl = document.getElementById('businessName');
        if (data.success) {
          messageBox.innerHTML = `
  <div id="successMsg" class="fade-out" style="display: flex; align-items: center; gap: 10px; color: green;">
    ${animatedCheckmarkSVG}
    <span>Business name updated successfully!</span>
  </div>
`;
        messageBox.innerHTML = `
  <div id="successMsg" class="fade-out" style="display: flex; align-items: center; gap: 10px; color: green;">
    ${animatedCheckmarkSVG}
    <span>Business name updated successfully!</span>
  </div>
`;

void messageBox.offsetWidth;

setTimeout(() => {
  const msg = document.getElementById('successMsg');
  if (msg) {
    msg.classList.add('hidden');
    
    msg.addEventListener('transitionend', function handler() {
      if (msg.parentNode) {
        msg.parentNode.removeChild(msg);
      }
      msg.removeEventListener('transitionend', handler);
    });
  }
}, 2000);

          nameEl.textContent = businessName;
          nameEl.classList.add('flash-success');
          setTimeout(() => nameEl.classList.remove('flash-success'), 1500);
        } else {
          messageBox.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px; color: red;">
              ${animatedErrorSVG}
              <span>${data.error}</span>
            </div>
          `;
        }
      });
  });


  document.getElementById('passwordForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const statusDiv = document.getElementById('status');

      if (newPassword !== confirmPassword) {
        statusDiv.innerHTML = `${errorSVG}<span>Passwords do not match</span>`;
        statusDiv.classList.add("show");
        return;
      }

      fetch('/Assignment/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'changePassword', apiKey, newPassword })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            statusDiv.innerHTML = `${successSVG}<span>Password updated!</span>`;
            statusDiv.classList.add("show");


            document.getElementById('newPassword').value = "";
            document.getElementById('confirmPassword').value = "";


            setTimeout(() => {
              statusDiv.classList.remove("show");
            }, 2000);
          } else {
            statusDiv.innerHTML = `${errorSVG}<span>${data.error || "Something went wrong."}</span>`;
            statusDiv.classList.add("show");
          }
        })
        .catch(() => {
          statusDiv.innerHTML = `${errorSVG}<span>Network error</span>`;
          statusDiv.classList.add("show");
        });
    });
}

const productForm = document.getElementById("productForm");

if (!productForm.dataset.listenerAdded) {

document.getElementById("productForm").addEventListener("submit", function (e) {
  console.log('Sending addProduct API request');

  e.preventDefault();

  const form = e.target;
  const apiKey = localStorage.getItem("api_key");
  if (!apiKey) {
    alert("You must be logged in as a manager to add a product.");
    return;
  }

  const formData = new FormData(form);
  const productData = {
  Name: formData.get("Name"),
  Brand: formData.get("Brand"),
  Material: formData.get("Material"),
  Description: formData.get("Description"),
  Color: formData.get("Color"),
  Price: parseFloat(formData.get("Price")),
  Stock: (formData.get("Stock")),
  ImageURL: formData.get("ImageURL"),
  Currency: "USD",
  Size: "M",
  Rating: 0,
  Tags: formData.get("Tags") || ""
};



  showLoader();
const payload = {
  type: "addProductManager",
  api_key: apiKey,
  data: productData 
};

fetch("/Assignment/api.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(payload)
})

    .then(res => res.json())
    .then(response => {
      hideLoader();
      if (response.status === "success") {

        form.reset();
        fetchAndRenderProducts(apiKey);
      } else {
        alert("Error: " + response.message);
      }
    })
    .catch(error => {
      hideLoader();
      alert("Failed to add product. Please try again.");
      console.error(error);
    });
  productForm.dataset.listenerAdded = "true";


});
}

document.getElementById("delete-account-btn").addEventListener("click", async function () {
    if (!confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
        return;
    }

    const apikey = localStorage.getItem("api_key");
    if (!apikey) {
        alert("API key not found. You must be logged in.");
        return;
    }

    const response = await fetch("/Assignment/api.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            type: "Delete",
            api_key: apikey
        })
    });

    const result = await response.json();

    if (result.status === "success") {
        alert("Your account has been deleted.");
        localStorage.removeItem("api_key");
        localStorage.clear();

        window.location.href = "index.php";
    } else {
        alert("Error deleting account: " + (result.message || "Unknown error."));
    }
});
const successSVG = `
      <svg viewBox="0 0 24 24" fill="none" stroke="#00ff88" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 6L9 17l-5-5" />
      </svg>
    `;

    const errorSVG = `
      <svg viewBox="0 0 24 24" fill="none" stroke="#ff5555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
    `;
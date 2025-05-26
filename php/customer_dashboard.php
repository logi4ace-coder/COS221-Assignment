<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customer Dashboard</title>
  <style>
    :root {
      --bg-color: #f2fff0;
      --text-color: #333333;
      --border-color: #e0e0e0;
      --form-bg: #ffffff;
      --input-bg: #ffffff;
      --footer-bg: #f8f9fa;
      --footer-border: #e0e0e0;
      --header-bg: #ffffff;
      --header-text: #333333;
      --header-border: #e0e0e0;
      --accent-color: #42ff33;
      --button-text: #f5f5f5;
      --button-hover-text: #42ff33;
      --shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    [data-theme="dark"] {
      --bg-color: hwb(0 7% 93%);
      --text-color: #f0f0f0;
      --border-color: #5e5a5a;
      --form-bg: #303134;
      --input-bg: #303134;
      --footer-bg: #202124;
      --footer-border: #333333;
      --header-bg: #202124;
      --header-text: #f0f0f0;
      --header-border: #333333;
      --accent-color: #42ff33;
      --button-text: #121212;
      --button-hover-text: #42ff33;
      --shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: all 0.3s ease;
    }
    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 2rem;
      background: var(--form-bg);
      border-radius: 16px;
      box-shadow: var(--shadow);
    }
    h1 {
      margin-bottom: 1.5rem;
      text-align: center;
      color: var(--accent-color);
      font-weight: 600;
    }
    h2 {
      color: var(--accent-color);
      border-bottom: 2px solid var(--border-color);
      padding-bottom: 0.5rem;
      margin-top: 0;
    }
    h3 {
      color: var(--text-color);
      margin-bottom: 1rem;
    }
    nav {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin-bottom: 2rem;
      border-bottom: 2px solid var(--border-color);
      padding-bottom: 1rem;
      flex-wrap: wrap;
    }
    nav button {
      background: none;
      border: none;
      font-weight: 600;
      font-size: 1rem;
      padding: 0.75rem 1.5rem;
      cursor: pointer;
      color: var(--text-color);
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    nav button:hover {
      background-color: rgba(66, 255, 51, 0.1);
    }
    nav button.active {
      color: var(--accent-color);
      background-color: rgba(66, 255, 51, 0.2);
    }
    section {
      background: var(--form-bg);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: var(--shadow);
      margin-bottom: 2rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1rem;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }
    th {
      background: var(--header-bg);
      font-weight: 600;
    }
    tr:hover {
      background-color: rgba(66, 255, 51, 0.05);
    }
    a {
      color: var(--accent-color);
      text-decoration: none;
      font-weight: 500;
    }
    a:hover {
      text-decoration: underline;
    }
    .small-text {
      font-size: 0.9rem;
      color: var(--text-color);
      opacity: 0.8;
    }
    button {
      background-color: var(--accent-color);
      color: var(--button-text);
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
      margin-right: 0.5rem;
    }
    button:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }
    button.secondary {
      background-color: transparent;
      color: var(--accent-color);
      border: 1px solid var(--accent-color);
    }
    .price-up {
      color: #ff3333;
    }
    .price-down {
      color: #33ff33;
    }
    .price-neutral {
      color: var(--text-color);
    }
    .theme-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      background: var(--accent-color);
      color: var(--button-text);
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      z-index: 100;
    }
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    .status-active {
      background-color: rgba(66, 255, 51, 0.2);
      color: var(--accent-color);
    }
    .status-inactive {
      background-color: rgba(255, 51, 51, 0.2);
      color: #ff3333;
    }
    .chart-container {
      height: 300px;
      background-color: var(--input-bg);
      border-radius: 8px;
      padding: 1rem;
      margin-top: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--border-color);
    }
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 1.5rem;
    }
    .product-card {
      background: var(--form-bg);
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--border-color);
      transition: transform 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-5px);
    }
    .product-name {
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    .product-price {
      font-size: 1.2rem;
      font-weight: 700;
      margin: 0.5rem 0;
    }
    .product-meta {
      font-size: 0.85rem;
      color: var(--text-color);
      opacity: 0.8;
    }
    .password-form {
      display: none;
      margin-top: 1.5rem;
      background: var(--input-bg);
      padding: 1.5rem;
      border-radius: 8px;
      border: 1px solid var(--border-color);
    }
    .password-form label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    .password-form input {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border-radius: 6px;
      border: 1px solid var(--border-color);
      background: var(--input-bg);
      color: var(--text-color);
    }
  </style>
</head>
<body>

<button class="theme-toggle" id="themeToggle">Toggle Dark Mode</button>

<div class="container">
  <h1>Customer Dashboard</h1>

  <nav>
    <button class="active" data-tab="watchlist">Watchlist</button>
    <button data-tab="alerts">Price Alerts</button>
    <button data-tab="history">Price History</button>
    <button data-tab="recommendations">Recommendations</button>
    <button data-tab="profile">Profile Info</button>
    <button data-tab="settings">Settings</button>
  </nav>

  <section id="watchlist" class="tab-content">
    <h2>Tracked Products</h2>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Current Price</th>
          <th>Price Change</th>
          <th>Previous Price</th>
          <th>Store</th>
          
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Wireless Noise-Cancelling Headphones</td>
          <td>$159.99</td>
          <td class="price-down">↓ $15.00 (8%)</td>
          <td>$140.00</td>
          <td>$174.99</td>
          <td><a href="#" target="_blank">Amazon</a></td>
          
        </tr>
        <tr>
          <td>Smart Watch Pro 2023</td>
          <td>$229.99</td>
          <td class="price-up">↑ $10.00 (4%)</td>
          <td>$200.00</td>
          <td>$219.99</td>
          <td><a href="#" target="_blank">Best Buy</a></td>
          
        </tr>
        <tr>
          <td>4K Ultra HD Smart TV 55"</td>
          <td>$499.99</td>
          <td class="price-down">↓ $50.00 (9%)</td>
          <td>$450.00</td>
          <td>$549.99</td>
          <td><a href="#" target="_blank">Walmart</a></td>
          
        </tr>
      </tbody>
    </table>
    
    <div class="product-grid">
      <div class="product-card">
        <div class="product-name">Wireless Earbuds Pro</div>
        <div class="product-price">$89.99</div>
        <div class="product-meta">Last price: $99.99</div>
        <div class="product-meta price-down">↓ $10.00 (10%)</div>
        <div class="product-meta">Alert at: $79.99</div>
        <button style="margin-top: 1rem;">View Details</button>
      </div>
      <div class="product-card">
        <div class="product-name">Fitness Tracker</div>
        <div class="product-price">$59.99</div>
        <div class="product-meta">Last price: $59.99</div>
        <div class="product-meta price-neutral">No change</div>
        <div class="product-meta">Alert at: $49.99</div>
        <button style="margin-top: 1rem;">View Details</button>
      </div>
      <div class="product-card">
        <div class="product-name">Bluetooth Speaker</div>
        <div class="product-price">$79.99</div>
        <div class="product-meta">Last price: $89.99</div>
        <div class="product-meta price-down">↓ $10.00 (11%)</div>
        <div class="product-meta">Alert at: $69.99</div>
        <button style="margin-top: 1rem;">View Details</button>
      </div>
    </div>
  </section>

  <section id="alerts" class="tab-content" style="display:none;">
    <h2>Price Alerts</h2>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Current Price</th>
          <th>Target Price</th>
          <th>Status</th>
          <th>Last Alert</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Wireless Headphones</td>
          <td>$159.99</td>
          <td>$140.00</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>Today, 10:45 AM</td>
          <td><button>Edit</button> <button class="secondary">Disable</button></td>
        </tr>
        <tr>
          <td>Smart Watch Pro</td>
          <td>$229.99</td>
          <td>$200.00</td>
          <td><span class="status-badge status-inactive">Inactive</span></td>
          <td>May 20, 2023</td>
          <td><button>Edit</button> <button class="secondary">Enable</button></td>
        </tr>
        <tr>
          <td>4K Smart TV</td>
          <td>$499.99</td>
          <td>$450.00</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>Yesterday, 3:22 PM</td>
          <td><button>Edit</button> <button class="secondary">Disable</button></td>
        </tr>
      </tbody>
    </table>
  </section>

  <section id="history" class="tab-content" style="display:none;">
    <h2>Price History</h2>
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
      <select style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color);">
        <option>Wireless Headphones</option>
        <option>Smart Watch Pro</option>
        <option>4K Smart TV</option>
        <option>Gaming Laptop</option>
        <option>Wireless Charger</option>
      </select>
      <select style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-color);">
        <option>Last 30 days</option>
        <option>Last 90 days</option>
        <option>Last 6 months</option>
        <option>Last year</option>
        <option>All time</option>
      </select>
    </div>
    
    <div class="chart-container">
      <p>Price chart visualization would appear here</p>
    </div>
    
    <table style="margin-top: 2rem;">
      <thead>
        <tr>
          <th>Date</th>
          <th>Price</th>
          <th>Change</th>
          <th>Store</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>May 24, 2023</td>
          <td>$159.99</td>
          <td class="price-down">↓ $5.00</td>
          <td>Amazon</td>
        </tr>
        <tr>
          <td>May 20, 2023</td>
          <td>$164.99</td>
          <td class="price-up">↑ $10.00</td>
          <td>Amazon</td>
        </tr>
        <tr>
          <td>May 15, 2023</td>
          <td>$154.99</td>
          <td class="price-down">↓ $20.00</td>
          <td>Best Buy</td>
        </tr>
        <tr>
          <td>May 10, 2023</td>
          <td>$174.99</td>
          <td class="price-neutral">No change</td>
          <td>Amazon</td>
        </tr>
        <tr>
          <td>May 5, 2023</td>
          <td>$174.99</td>
          <td class="price-up">↑ $25.00</td>
          <td>Walmart</td>
        </tr>
      </tbody>
    </table>
  </section>

  <section id="recommendations" class="tab-content" style="display:none;">
    <h2>Recommended Products</h2>
    <div class="product-grid">
      <div class="product-card">
        <div class="product-name">Smart Thermostat</div>
        <div class="product-price">$129.99 <span class="product-meta" style="text-decoration: line-through;">$149.99</span></div>
        <div class="product-meta price-down">↓ $20.00 (13%)</div>
        <div class="product-meta">Based on your interest in smart home devices</div>
        <button style="margin-top: 1rem;">Track Price</button>
      </div>
      <div class="product-card">
        <div class="product-name">External SSD 1TB</div>
        <div class="product-price">$89.99 <span class="product-meta" style="text-decoration: line-through;">$109.99</span></div>
        <div class="product-meta price-down">↓ $20.00 (18%)</div>
        <div class="product-meta">Similar to products you track</div>
        <button style="margin-top: 1rem;">Track Price</button>
      </div>
      <div class="product-card">
        <div class="product-name">Wireless Keyboard</div>
        <div class="product-price">$49.99 <span class="product-meta" style="text-decoration: line-through;">$59.99</span></div>
        <div class="product-meta price-down">↓ $10.00 (17%)</div>
        <div class="product-meta">Popular among users like you</div>
        <button style="margin-top: 1rem;">Track Price</button>
      </div>
      <div class="product-card">
        <div class="product-name">Air Fryer XL</div>
        <div class="product-price">$79.99 <span class="product-meta" style="text-decoration: line-through;">$99.99</span></div>
        <div class="product-meta price-down">↓ $20.00 (20%)</div>
        <div class="product-meta">Limited time deal</div>
        <button style="margin-top: 1rem;">Track Price</button>
      </div>
    </div>
  </section>

  <section id="profile" class="tab-content" style="display:none;">
    <h2>Profile Information</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
      <div>
        <h3>Account Details</h3>
        <div style="background: var(--input-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
          <p><strong>Name:</strong> Jane Doe</p>
          <p><strong>Email:</strong> jane.doe@example.com</p>
          <p><strong>Member since:</strong> January 15, 2022</p>
          <p><strong>Last login:</strong> May 24, 2023 at 3:45 PM</p>
          <button id="editProfileBtn" style="margin-top: 1rem;">Edit Profile</button>
        </div>
      </div>
      
      </div>
    </div>
    
    <div class="password-form" id="passwordForm">
      <h3>Change Password</h3>
      <div>
        <label for="current-password">Current Password</label>
        <input type="password" id="current-password">
      </div>
      <div>
        <label for="new-password">New Password</label>
        <input type="password" id="new-password">
      </div>
      <div>
        <label for="confirm-password">Confirm New Password</label>
        <input type="password" id="confirm-password">
      </div>
      <button>Update Password</button>
    </div>
  </section>

  <section id="settings" class="tab-content" style="display:none;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
      
      <div>
        <h3>Account Management</h3>
        <div style="background: var(--input-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
          <p><strong>Data Export:</strong> Download all your data</p>
          <button>Export Data</button>
          
          <p style="margin-top: 1.5rem;"><strong>Delete Account:</strong> Permanently remove your account</p>
          <button class="secondary">Delete Account</button>
        </div>
      </div>
    </div>
    
    
  </section>
</div>

<script>
  // Tab switching functionality
  const tabs = document.querySelectorAll('nav button');
  const sections = document.querySelectorAll('.tab-content');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      tabs.forEach(btn => btn.classList.remove('active'));
      tab.classList.add('active');

      const target = tab.getAttribute('data-tab');
      sections.forEach(section => {
        section.style.display = section.id === target ? 'block' : 'none';
      });
    });
  });

  // Theme switching functionality
  const themeToggle = document.getElementById('themeToggle');
  const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

  function setTheme(theme) {
    if (theme === 'system') {
      theme = prefersDarkScheme.matches ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    updateButtonText();
  }

  // Check for saved theme preference or use system preference
  const currentTheme = localStorage.getItem('theme') || 'light';
  setTheme(currentTheme);

  themeToggle.addEventListener('click', () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
  });

  // Update button text based on current theme
  function updateButtonText() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    themeToggle.textContent = currentTheme === 'dark' ? 'Toggle Light Mode' : 'Toggle Dark Mode';
  }

  // Password form toggle
  const editProfileBtn = document.getElementById('editProfileBtn');
  const passwordForm = document.getElementById('passwordForm');

  editProfileBtn.addEventListener('click', () => {
    passwordForm.style.display = passwordForm.style.display === 'block' ? 'none' : 'block';
  });
</script>

</body>
</html>
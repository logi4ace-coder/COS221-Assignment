<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/Dashboard.css">
    <title>Dashboard</title>
    
</head>
<body>
    <header class="header">
            <div class="logo">
            
                COMPARE<span>X</span>IT
            </div>
            
           <div class="nav-links">
                <ul>
                    <li><a href="index.html" >Products</a></li>
                    <li><a href="login.html" >Log-in</a></li>
                    <li><a href="Signin.html" class="active" >Sign-in</a></li>
                    <li><a href="wishlist.html" >Wishlist</a></li>
                    <li><a href="Dashboard.html" >Dashboard</a></li>
                </ul>
            </div>
    </header>

    <main class="Dashboard-container">
    <div class="container">

        <div class="header-section">
            <div class="welcome-section">
                <h1>Welcome back, Manager!</h1>
                <div class="business-name">Business: <span id="businessName">Compony Name.</span></div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openModal('businessModal')">Update Business Name</button>
                <button class="btn btn-warning" onclick="openModal('passwordModal')">Change Password</button>
                <button class="btn btn-danger" >Delete Account</button>
                <button class="btn btn-secondary" onclick="logout()">Log Out</button>
            </div>
        </div>

        <div class="section">
            <h2>Add New Product</h2>
            <form id="productForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" name="productName" required>
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Price ($)</label>
                        <input type="number" id="productPrice" name="productPrice" step="0.01" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="productStore">Store</label>
                        <input type="text" id="productStore" name="productStore" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Category</label>
                        <select id="productCategory" name="productCategory" required>
                            <option value="">Select Category</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                            <option value="home">Home & Garden</option>
                            <option value="sports">Sports & Outdoors</option>
                            <option value="books">Books</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="productDescription">Description</label>
                    <textarea id="productDescription" name="productDescription" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Add Product</button>
            </form>
        </div>

        <div class="section">
            <h2>Product Management</h2>
            <div id="productList" class="product-grid">
                 <div class="product-card">
                    <h3>product name</h3>
                    <div class="product-info">
                        <p><strong>Price:</strong> product price</p>
                        <p><strong>Store:</strong> product store</p>
                        <p><strong>Category:</strong> product category</p>
                        <p><strong>Description:</strong> product description</p>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-primary" >Edit Price</button>
                        <button class="btn btn-danger" >Remove</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="section">
            <h2>Price Alerts</h2>
            <div id="alertsList">
                <div class="alert-item">
                    <div class="alert-info">
                        <div class="alert-product">alert productName</div>
                        <div class="alert-price"> alert requestedPrice </div>
                        <div class="alert-user"> alert userEmail</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="businessModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('businessModal')">&times;</span>
            <h2>Update Business Name</h2>
            <form id="businessForm">
                <div class="form-group">
                    <label for="newBusinessName">New Business Name</label>
                    <input type="text" id="newBusinessName" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>


    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('passwordModal')">&times;</span>
            <h2>Change Password</h2>
            <form id="passwordForm">
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" required>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>

    </main>

    <footer class="footer"> 
        <div class="footer-info">
            @2025 COMPARE<span>X</span>IT. All rights reserved.
        </div>
    
        <div class="theme-select">
            <button id="theme-Dark"><i class="fas fa-moon"></i> Dark</button>
            <button id="theme-Light"><i class="fas fa-sun"></i> Light</button>
        </div>
    </footer>

    <script>

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        document.getElementById('businessForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const newName = document.getElementById('newBusinessName').value;
            document.getElementById('businessName').textContent = newName;
            closeModal('businessModal');
            alert('Business name updated successfully!');
        });


        function logout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'index.html'; 

            }
        }
    </script>
    <script src="script/theme.js" defer></script>
</body>
</html>
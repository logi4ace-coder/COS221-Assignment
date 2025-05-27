:root {
    --primary-light: #42ff33;
    --bg-light: rgb(255, 245, 247);
    --bg-dark: #1A1A24;
    --text-light: rgb(65, 81, 97);
    --text-dark: #E0E0E0;
    --bg-white: white;
    --border-color: #e0e0e0;
}

/* Base Styles */
body {
    font-family: 'Barlow', sans-serif;
    background-color: var(--bg-light);
    color: var(--text-light);
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Main Container */
.container {
    max-width: 1200px;
    width: 90%;
    margin: 0 auto;
    padding: 0 20px;
    flex: 1;
}

/* Header Styles */
header {
    position: fixed;
    top: 0;
    width: 100%;
    background-color: var(--bg-white);
    padding: 15px 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 1000;
}

.header-container {
    max-width: 1200px;
    width: 90%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.company-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.company-logo {
    width: 42px;
    height: 42px;
    object-fit: contain;
}

.company-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-light);
    margin: 0;
    white-space: nowrap;
}

/* Navigation */
.nav-links {
    display: flex;
    margin-left: auto;
}

.nav-links ul {
    list-style-type: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
}

.nav-links ul li a {
    text-decoration: none;
    color: var(--text-light);
    transition: color 0.2s;
    font-size: 1rem;
    font-weight: 500;
}

.nav-links ul li a:hover {
    color: var(--primary-light);
}

/* Theme Selector */
.theme-select {
    margin-left: 30px;
}

.theme-select button {
    background-color: var(--primary-light);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}

/* Product Container */
#product-container {
    width: 100%;
    background-color: var(--bg-white);
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 100px;
    margin-bottom: 30px;
    padding: 25px;
}

/* Product Image */
#product-container img {
    width: 100%;
    max-height: 500px;
    object-fit: contain;
    margin: 0 auto 25px;
    display: block;
    border-radius: 8px;
    background-color: var(--bg-light);
}

/* Product Details */
#product-container h2 {
    font-size: 28px;
    color: var(--text-light);
    margin-bottom: 20px;
    text-align: center;
}

.product-meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
}

.product-meta p {
    margin: 0;
    color: var(--text-light);
    font-size: 16px;
}

.product-meta strong {
    font-weight: 600;
}

.product-description {
    line-height: 1.6;
    color: var(--text-light);
    margin-bottom: 30px;
    text-align: center;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

/* Listings Section */
.listings-section {
    margin-top: 40px;
}

.listings-section h3 {
    font-size: 22px;
    color: var(--text-light);
    margin-bottom: 20px;
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
}

.listing-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    margin-bottom: 15px;
}

.listing-info {
    flex: 1;
}

.listing-retailer {
    font-weight: 600;
    color: var(--text-light);
    margin-bottom: 5px;
}

.listing-price {
    font-weight: 700;
    color: var(--primary-light);
    font-size: 18px;
}

.visit-retailer {
    background-color: var(--primary-light);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
}

/* Review Section */
.review-section {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid var(--border-color);
}

.review-section h3 {
    font-size: 22px;
    color: var(--text-light);
    margin-bottom: 20px;
    text-align: center;
}

/* Review Form */
#review-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 25px;
    background-color: var(--bg-white);
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

#review-form label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-light);
    font-weight: 500;
}

#star-rating {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin: 15px 0;
}

#star-rating span {
    font-size: 28px;
    color: #f59e0b;
    cursor: pointer;
    transition: transform 0.2s;
}

#star-rating span:hover {
    transform: scale(1.1);
}

#review_text {
    width: 100%;
    min-height: 120px;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    margin-bottom: 20px;
    font-family: 'Barlow', sans-serif;
}

#review-form button {
    display: block;
    width: 100%;
    background-color: var(--primary-light);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
}

/* Reviews Content */
#reviews-content {
    margin-top: 30px;
}

.review {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: var(--bg-white);
}

.review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.review-rating {
    margin-bottom: 15px;
}

.review-text {
    line-height: 1.6;
}

/* Loader */
.loader-img {
    display: block;
    margin: 30px auto;
    width: 50px;
    height: 50px;
}

/* Dark Theme */
[data-theme="dark"] {
    --bg-white: var(--bg-dark);
    --bg-light: var(--bg-dark);
    --text-light: var(--text-dark);
    --border-color: #444;
}

body.dark-theme {
    background-color: var(--bg-dark);
}

body.dark-theme header,
body.dark-theme #product-container,
body.dark-theme #review-form,
body.dark-theme .review {
    background-color: var(--bg-dark);
    border-color: #555;
}

body.dark-theme .company-name,
body.dark-theme .nav-links ul li a {
    color: var(--text-dark);
}

body.dark-theme #review_text {
    background-color: #303134;
    color: var(--text-dark);
    border-color: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        width: 95%;
        padding: 0 15px;
    }
    
    .header-container {
        flex-direction: column;
        gap: 15px;
        padding: 15px 0;
    }
    
    .company-brand {
        position: static;
        transform: none;
        margin-bottom: 10px;
    }
    
    .nav-links {
        margin: 15px 0;
    }
    
    .nav-links ul {
        gap: 15px;
    }
    
    .theme-select {
        margin-left: 0;
    }
    
    #product-container {
        margin-top: 150px;
        padding: 20px;
    }
    
    .listing-item {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .visit-retailer {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .company-brand {
        flex-direction: column;
        text-align: center;
    }
    
    .nav-links ul {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    #product-container {
        margin-top: 180px;
    }
}

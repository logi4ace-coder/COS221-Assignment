<?php
ini_set('memory_limit', '1G');
set_time_limit(7200);

$mysqli = new mysqli("wheatley.cs.up.ac.za", "u24742083", "Y55NX6I5EQGJ4WUVGN6M2ZA2DD73PYOS", "u24742083_CompareIt");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$insertedUsers = [];
$insertedTags = [];
$insertedRetailers = [];

function generateSalt($length = 16) {
    return bin2hex(random_bytes($length));
}

$plaintextPassword = 'Asdfghjkl7*'; // Default seeded password for users

$fp = fopen("productsApi.json", "r");
if (!$fp) {
    die("Could not open file.");
}

// Skip first character: the opening [
fgetc($fp);

$buffer = '';
$depth = 0;

while (!feof($fp)) {
    $char = fgetc($fp);
    if ($char === false) break;

    if ($char === '{') {
        $depth++;
    }
    if ($depth > 0) {
        $buffer .= $char;
    }
    if ($char === '}') {
        $depth--;
        if ($depth === 0) {
            $item = json_decode($buffer, true);
            $buffer = '';

            if (!$item) continue;
           
            

            // === Compute average rating for the product ===
            $totalRating = 0;
            $reviewCount = count($item['reviews']);
            foreach ($item['reviews'] as $review) {
                $totalRating += (int)$review['rating'];
            }
            $averageRating = ($reviewCount > 0) ? round($totalRating / $reviewCount) : 3;

            // === Insert Product ===
            $name = $mysqli->real_escape_string($item['name']);
            $brandName = isset($item['brand']['name']) ? $item['brand']['name'] : 'Makwaqasa';
            $brand = $mysqli->real_escape_string($brandName);
            $material = $mysqli->real_escape_string($item['material']);
            $description = $mysqli->real_escape_string($item['description']);
            $color = isset($item['color']) ? $mysqli->real_escape_string($item['color']) : 'Unknown';


            $stmt = $mysqli->prepare("INSERT INTO Products (Name, Brand, Material, Description, Rating, Color)
                                     VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
            }
            $stmt->bind_param("ssssds", $name, $brand, $material, $description, $averageRating, $color);
            if (!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            $productId = $stmt->insert_id;
            $stmt->close();

            // === Insert Tags ===
            // correct
            foreach ($item['styleTags'] as $tag) {
                $tag = $mysqli->real_escape_string($tag);
                if (!isset($insertedTags[$tag])) {
                    $stmt = $mysqli->prepare("INSERT INTO Tags (TagText) VALUES (?)");
                    if (!$stmt) die("Prepare failed: " . $mysqli->error);
                    $stmt->bind_param("s", $tag);
                    if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
                    $insertedTags[$tag] = $stmt->insert_id;
                    $stmt->close();
                }
                $tagId = $insertedTags[$tag];
            
                $stmt = $mysqli->prepare("INSERT IGNORE INTO ProductsTags (ProductID, TagID) VALUES (?, ?)");
                if (!$stmt) die("Prepare failed: " . $mysqli->error);
                $stmt->bind_param("ii", $productId, $tagId);
                if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
                $stmt->close();
            }
            

            // === Insert Retailers and Listings ===
                // === Insert Retailers and Listings ===
foreach ($item['stockists'] as $stockist) {
    $retailerName = $mysqli->real_escape_string($stockist['storeName']);
    $website = 'https://www.' . strtolower(preg_replace('/\s+/', '', $retailerName)) . '.com';

    // Handle manager user insertion or retrieval
    $manager = $stockist['manager'] ?? null;
    $managerUserId = null;
    if ($manager && isset($manager['email'])) {
        $managerEmail = strtolower(trim($manager['email']));
        if ($managerEmail !== '') {
            if (!isset($insertedUsers[$managerEmail])) {
                // Check if manager user exists
                $stmtCheckManager = $mysqli->prepare("SELECT UserID FROM Users WHERE Email = ?");
                if (!$stmtCheckManager) die("Prepare failed: " . $mysqli->error);
                $stmtCheckManager->bind_param("s", $managerEmail);
                $stmtCheckManager->execute();
                $stmtCheckManager->store_result();

                if ($stmtCheckManager->num_rows > 0) {
                    $stmtCheckManager->bind_result($managerUserId);
                    $stmtCheckManager->fetch();
                    $insertedUsers[$managerEmail] = $managerUserId;
                } else {
                    // Insert new manager user as admin
                    $firstName = $mysqli->real_escape_string($manager['firstName'] ?? 'Manager');
                    $lastName = $mysqli->real_escape_string($manager['lastName'] ?? '');
                    $apiKey = uniqid('api_', true);

                    $salt1 = generateSalt(16);
                    $salt2 = generateSalt(8);
                    $passwordToHash = $salt2 . $salt1 . $plaintextPassword;
                    $hashedPassword = password_hash($passwordToHash, PASSWORD_ARGON2ID);

                    $stmtInsertManager = $mysqli->prepare("INSERT INTO Users (FirstName, LastName, Email, UserType, PasswordHash, salt1, salt2, ApiKey)
                                                          VALUES (?, ?, ?, 'admin', ?, ?, ?, ?)");
                    if (!$stmtInsertManager) die("Prepare failed: " . $mysqli->error);
                    $stmtInsertManager->bind_param("sssssss", $firstName, $lastName, $managerEmail, $hashedPassword, $salt1, $salt2, $apiKey);
                    if (!$stmtInsertManager->execute()) die("Execute failed: " . $stmtInsertManager->error);

                    $managerUserId = $stmtInsertManager->insert_id;
                    $insertedUsers[$managerEmail] = $managerUserId;
                    $stmtInsertManager->close();
                }
                $stmtCheckManager->close();
            } else {
                $managerUserId = $insertedUsers[$managerEmail];
            }
        }
    }

    if (!isset($insertedRetailers[$retailerName])) {
        $country = 'Unknown';
        $type = 'online';
        $address = $retailerName . " HQ";

        $stmt = $mysqli->prepare("INSERT INTO Retailers (Name, Country, Type, WebsiteURL, StoreAddress, IsActive, ManagerUserID)
                                 VALUES (?, ?, ?, ?, ?, 1, ?)");
        if (!$stmt) die("Prepare failed: " . $mysqli->error);
        $stmt->bind_param("sssssi", $retailerName, $country, $type, $website, $address, $managerUserId);
        if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
        $insertedRetailers[$retailerName] = $stmt->insert_id;
        $stmt->close();
    }
    $retailerId = $insertedRetailers[$retailerName];

    $price = (float)$stockist['price'];
    $productUrl = $website . "/product/" . urlencode($name);
    $currency = strtoupper($item['currency'] ?? 'USD');
    $lastUpdated = date("Y-m-d H:i:s");

    $stmt = $mysqli->prepare("INSERT IGNORE INTO Listings (ProductID, RetailerID, ProductURL, Price, LastUpdated, Size, Stock, Currency)
                             VALUES (?, ?, ?, ?, ?, 'M', 'in_stock', ?)");
    if (!$stmt) die("Prepare failed: " . $mysqli->error);
    $stmt->bind_param("iisdss", $productId, $retailerId, $productUrl, $price, $lastUpdated, $currency);
    if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
    $stmt->close();
}


            $imageData = $item['images'] ?? [$item['image'] ?? null];

            foreach ($imageData as $i => $imgUrl) {
                if (!$imgUrl) continue;// retriving correctly

                $imgUrl = $mysqli->real_escape_string($imgUrl);
                $isPrimary = ($i === 0) ? 1 : 0;

                $stmt = $mysqli->prepare("INSERT INTO ProductImages (ProductID, IsPrimary, ImageURL)
                                        VALUES (?, ?, ?)");
                if (!$stmt) die("Prepare failed: " . $mysqli->error);
                $stmt->bind_param("iis", $productId, $isPrimary, $imgUrl);
                if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
                $stmt->close();
            }


          // === Insert Reviews ===

            foreach ($item['reviews'] as $review) {// already fixed i am retriving correctly
        $email = strtolower(trim($review['email']));
        if ($email === '') continue;
        
        if (!isset($insertedUsers[$email])) {
            // Check if user exists in DB
            $stmtCheck = $mysqli->prepare("SELECT UserID FROM Users WHERE Email = ?");
            if (!$stmtCheck) die("Prepare failed: " . $mysqli->error);
            $stmtCheck->bind_param("s", $email);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->bind_result($userId);
                $stmtCheck->fetch();
                $insertedUsers[$email] = $userId;
            } else {
                // Insert new user
                $firstName = $mysqli->real_escape_string($review['firstName']);
                $lastName = $mysqli->real_escape_string($review['lastName']);
                $apiKey = uniqid('api_', true);

                $salt1 = generateSalt(16);
                $salt2 = generateSalt(8);
                $passwordToHash = $salt2 . $salt1 . $plaintextPassword;
                $hashedPassword = password_hash($passwordToHash, PASSWORD_ARGON2ID);

                $stmtInsert = $mysqli->prepare("INSERT INTO Users (FirstName, LastName, Email, UserType, PasswordHash, salt1, salt2, ApiKey)
                                            VALUES (?, ?, ?, 'customer', ?, ?, ?, ?)");
                if (!$stmtInsert) die("Prepare failed: " . $mysqli->error);
                $stmtInsert->bind_param("sssssss", $firstName, $lastName, $email, $hashedPassword, $salt1, $salt2, $apiKey);
                if (!$stmtInsert->execute()) die("Execute failed: " . $stmtInsert->error);

                $insertedUsers[$email] = $stmtInsert->insert_id;
                $stmtInsert->close();
            }
            $stmtCheck->close();
        }
    }

        
        
        }
    }
}

fclose($fp);
echo "Streaming seeding complete.\n";

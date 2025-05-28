<?php

session_start();

require_once("../../Assignment/configs/keys.php"); 
require_once("../../Assignment/configs/recaptcha.php"); 
require_once("../../Assignment/configs/config.php"); 
require_once '../../Assignment/configs/encryption.php';
require_once '../../Assignment/configs/email_credentials.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

$mail = new PHPMailer(true);

class UserAPI {

    private $connect;
    private static $instance = null;

    private function __construct($db_connection) {
        $this->connect = $db_connection;
        error_log("UserAPI instance created.");
    }

    public static function getter($db_connection = null) {
        error_log("UserAPI::getter() called.");
        if (self::$instance === null) {
            if ($db_connection === null) {
                throw new Exception('Database connection was not made.');
            }
            self::$instance = new UserAPI($db_connection);
        }
        return self::$instance;
    }

    public function registerUser($data) {
    session_start();

    $missing_data = [];
    $required_fields = ['type', 'name', 'surname', 'email', 'password', 'user_type'];

    foreach ($required_fields as $parameter) {
        if (empty($data[$parameter])) {
            $missing_data[] = $parameter;
        }
    }

    if (!empty($missing_data)) {
        $_SESSION['errors'] = ["Missing fields: " . implode(", ", $missing_data)];
        header("Location: php/Signup.php");
        exit;
    }

    if ($data['type'] !== 'Register') {
        $_SESSION['errors'] = ["Invalid request type."];
        header("Location: php/Signup.php");
        exit;
    }

    $name = trim($data['name']);
    $surname = trim($data['surname']);
    $email = trim($data['email']);
    $password = $data['password'];
    $user_type_input = $data['user_type'];

    $user_type_map = [
        'Customer' => 'customer',
        'Manager'  => 'admin'
    ];

    if (!array_key_exists($user_type_input, $user_type_map)) {
        $_SESSION['errors'] = ["Invalid user type."];
        header("Location: php/Signup.php");
        exit;
    }

    $user_type = $user_type_map[$user_type_input];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors'] = ["Invalid email format."];
        $_SESSION['old'] = [
    'name' => $name,
    'surname' => $surname,
    'email' => $email,
    'user_type' => $user_type_input,
    'type' => $data['type']
];
        header("Location: php/Signup.php");
        exit;
    }

    $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($password_regex, $password)) {
        $_SESSION['errors'] = [
            "Password must be at least 8 characters long, contain upper and lowercase letters, at least one digit, and one special character."
        ];
        $_SESSION['old'] = [
    'name' => $name,
    'surname' => $surname,
    'email' => $email,
    'user_type' => $user_type_input,
    'type' => $data['type']
];
        header("Location: php/Signup.php");
        exit;
    }

    if ($this->emailDuplicationCheck($email)) {
        $_SESSION['errors'] = ["Email already registered. Try another one."];
        $_SESSION['old'] = [
    'name' => $name,
    'surname' => $surname,
    
    'user_type' => $user_type_input,
    'type' => $data['type']
];
        header("Location: php/Signup.php");
        exit;
    }

    $salt1 = bin2hex(random_bytes(16));
    $salt2 = bin2hex(random_bytes(8));
    $hashed_password = password_hash($salt2 . $salt1 . $password, PASSWORD_ARGON2ID);
    $api_key = bin2hex(random_bytes(16));

    if ($this->insertUser($name, $surname, $email, $hashed_password, $user_type, $api_key, $salt1, $salt2)) {
        $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE Email = ?");
        if ($stmt === false) {
            $_SESSION['errors'] = ["Internal error after registration."];
            header("Location: php/Signup.php");
            exit;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['api_key'] = $api_key;
        $_SESSION['email'] = $email;


       if ($user_type === 'admin') {
            $_SESSION['show_modal'] = true;
            header("Location: php/Signup.php");
            exit;
        }

        header("Location: php/login.php");
        exit;

    } else {
        $_SESSION['errors'] = ["Error inserting user {$name} into database"];
        header("Location: php/Signup.php");
        exit;
    }
}

    
    

    private function emailDuplicationCheck($email) {
        error_log("Checking if email: $email already exists...");

        $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE Email = ?");
        if ($stmt === false) {
            error_log("SQL prepare statement error: " . $this->connect->error);
            return false;
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            error_log("SQL execute error: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    private function insertUser($name, $surname, $email, $hashed_password, $user_type, $api_key, $salt1, $salt2) {
        error_log("Inserting user into DB: $email");

        $stmt = $this->connect->prepare("
            INSERT INTO Users 
            (FirstName, LastName, Email, PasswordHash, UserType, ApiKey, salt1, salt2)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt === false) {
            error_log("SQL prepare statement error: " . $this->connect->error);
            return false;
        }

        $stmt->bind_param("ssssssss", $name, $surname, $email, $hashed_password, $user_type, $api_key, $salt1, $salt2);
        $result = $stmt->execute();

        if (!$result) {
            error_log("User insert failed: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }

    private function successJson($api_key) {
        return json_encode([
            "status" => "success",
            "timestamp" => round(time()),
            "data" => ["apikey" => $api_key]
        ]);
    }

    private function httpCodes($message) {
        http_response_code(400);
        return json_encode([
            "status" => "error",
            "timestamp" => time(),
            "message" => $message
        ]);
    }

    private function respondError($message) {
        error_log("respondError called: $message");
    $_SESSION['errors'] = ['validation' => [$message]];

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

            echo json_encode(['status' => 'error', 'message' => $message]);
    } else {

        header("Location: php/login.php");
    }

    exit;
}


    public function loginUser($data)
{
    // reCAPTCHA Verification
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = RECAPTCHA_SECRET_KEY; // in anoter file
    $url = "https://www.google.com/recaptcha/api/siteverify";

    $recaptchaData = [
        'secret' => $secretKey,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    $options = [
        'http' => [
            'method' => 'POST',
            'content' => http_build_query($recaptchaData),
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"] ?? 0) !== 1) {
        return $this->respondError("reCAPTCHA verification failed, please try again.");
    }


    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    error_log("Emial: {$email}"." {$password}");

    $missing = [];
    $errors = [];

    if (empty($email)) {
        $missing[] = 'email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $missing[] = 'password';
    } else {
        $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
        if (!preg_match($password_regex, $password)) {
            $errors['password'] = "Password must be at least 8 characters, include upper/lowercase, number, and special character.";
        }
    }

    if (!empty($missing) || !empty($errors)) {
        $_SESSION['form-input'] = ['email' => $email];
        $_SESSION['errors'] = ['missing' => $missing, 'validation' => $errors];
        return $this->respondError("Missing or invalid fields.");
    }


    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE Email=?");
    if (!$stmt) return $this->respondError("Database error: failed to prepare (user lookup)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) return $this->respondError("Email not found. Please register.");
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();


    $stmt = $this->connect->prepare("SELECT salt1, salt2, PasswordHash FROM Users WHERE UserID=?");
    if (!$stmt) return $this->respondError("Database error: failed to prepare (salt/password lookup)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) return $this->respondError("Account security information missing.");
    $stmt->bind_result($salt1, $salt2, $hashedPassword);
    $stmt->fetch();
    $stmt->close();


    $reconstructed = $salt2 . $salt1 . $password;
    if (!password_verify($reconstructed, $hashedPassword)) {
        return $this->respondError("Incorrect password.");
    }


    $stmt = $this->connect->prepare("SELECT ApiKey, UserType FROM Users WHERE Email=?");
    if (!$stmt) return $this->respondError("Database error: failed to prepare (api/role)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) return $this->respondError("Unable to fetch account metadata.");
    $stmt->bind_result($apiKey, $userType);
    $stmt->fetch();
    $stmt->close();

    // Start Secure Session
    session_regenerate_id(true); 
    $encryptedApiKey = encryptData($apiKey);

    $_SESSION['api_key'] = $encryptedApiKey;    

    $_SESSION['user_role'] = $userType;
    $_SESSION['user_email'] = $email;


    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

        echo json_encode(['success' => true, 'redirect' => 'php/index.php']);
        exit;
    } else {

        header("Location: php/index.php");
        exit;
    }
}

public function registerBusiness($data) {
    

    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        $_SESSION['errors'][] = 'Unauthorized access.';
        header('Location: php/Signup.php');
        exit;
    }

    $userId = $_SESSION['user_id'];

    $required = ['Name', 'Country', 'Type'];
    $missing = [];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        $_SESSION['errors'][] = 'Missing fields: ' . implode(', ', $missing);
        $_SESSION['old'] = $data;
        $_SESSION['show_modal'] = true;
        header('Location: php/login.php');
        exit;
    }

    if (!in_array($data['Type'], ['online', 'physical', 'hybrid'])) {
        $_SESSION['errors'][] = 'Invalid business type';
        $_SESSION['old'] = $data;
        $_SESSION['show_modal'] = true;
        header('Location: php/Signup.php');
        exit;
    }

    $stmt = $this->connect->prepare("INSERT INTO Retailers 
        (Name, Country, Type, WebsiteURL, StoreAddress, IsActive, ManagerUserID) 
        VALUES (?, ?, ?, ?, ?, 1, ?)");

    if (!$stmt) {
        $_SESSION['errors'][] = 'An error occured on our send, please try again later: ' . $this->connect->error;
        $_SESSION['old'] = $data;
        $_SESSION['show_modal'] = true;
        header('Location: php/Signup.php');
        exit;
    }

    $website = $data['WebsiteURL'] ?? null;
    $address = $data['StoreAddress'] ?? null;

    $stmt->bind_param(
        "sssssi",
        $data['Name'],
        $data['Country'],
        $data['Type'],
        $website,
        $address,
        $userId
    );

    try {
        $stmt->execute();

        $_SESSION['success'] = 'Business registered successfully.';
        header('Location: php/login.php'); 
        exit;
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['errors'][] = 'A business with this Name already exists.';
        } else {
            $_SESSION['errors'][] = 'Failed to register business: ' . $e->getMessage();
        }
        $_SESSION['old'] = $data;
        $_SESSION['show_modal'] = true;
        header('Location: php/Signup.php');
        exit;
    }
}


   public function deleteUser($data) {
    error_log("deleteUser called");

    $missing_data = [];
    $required_fields = ['type', 'api_key'];

    foreach ($required_fields as $parameter) {
        if (empty($data[$parameter])) {
            $missing_data[] = $parameter;
        }
    }

    if (!empty($missing_data)) {
        error_log("Missing fields: " . implode(", ", $missing_data));
        echo $this->httpCodes("Missing fields: " . implode(", ", $missing_data));
        return;
    }

    if ($data['type'] !== 'Delete') {
        error_log("Invalid type parameter: " . $data['type']);
        return $this->httpCodes("Invalid request type");
    }

    $encryptedApiKey = decryptData($data['api_key']);
    $apiKey = ($encryptedApiKey);
    
    if ($apiKey === false) {
        error_log("Failed to decrypt API key");
        return $this->httpCodes("Invalid API key");
    }

    $stmt = $this->connect->prepare("SELECT UserID, UserType FROM Users WHERE ApiKey = ?");
    if (!$stmt) {
        return $this->httpCodes("Internal error while preparing query.");
    }

    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $stmt->bind_result($userId, $userType);
    $stmt->fetch();
    $stmt->close();

    if (empty($userId)) {
        return $this->httpCodes("User not found or already deleted.");
    }

    if ($userType === 'customer') {
        $negativeUserId = -$userId;


        $stmt = $this->connect->prepare("UPDATE Reviews SET UserID = ? WHERE UserID = ?");
        $stmt->bind_param("ii", $negativeUserId, $userId);
        $stmt->execute();
        $stmt->close();


        $stmt = $this->connect->prepare("DELETE FROM Wishlists WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();


        $stmt = $this->connect->prepare("DELETE FROM PriceAlerts WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();


        


        $stmt = $this->connect->prepare("DELETE FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();


        $stmt = $this->connect->prepare("
            INSERT INTO Users (UserID, FirstName, LastName, Email, UserType, PasswordHash, ApiKey, CreatedAt, salt1, salt2)
            VALUES (?, 'Deleted', 'User', '', 'customer', '', '', NOW(), '', '')
        ");
        $stmt->bind_param("i", $negativeUserId);
        $stmt->execute();
        $stmt->close();

        error_log("Customer deleted and anonymized with ID: $negativeUserId");

        return json_encode([
            "status" => "success",
            "timestamp" => time(),
            "data" => [
                "deleted_user_id" => $negativeUserId,
                "original_user_type" => $userType
            ]
        ]);

    } else if ($userType === 'admin') {

        $stmt = $this->connect->prepare("DELETE FROM Retailers WHERE ManagerUserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();


        $stmt = $this->connect->prepare("DELETE FROM UserPreferences WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();


        $stmt = $this->connect->prepare("DELETE FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        error_log("Admin user and associated retailers deleted: $userId");

        return json_encode([
            "status" => "success",
            "timestamp" => time(),
            "data" => [
                "deleted_user_id" => $userId,
                "original_user_type" => $userType
            ]
        ]);
    } else {
        return $this->httpCodes("Unknown user type: $userType");
    }
   }

public function getAllProducts($managerApiKey) {
    $decryptedApiKey = decryptData($managerApiKey);
    if ($decryptedApiKey === false) {
        return $this->httpCodes("Invalid API key");
    }

    //Find retailer(s) managed by this user
    $stmt = $this->connect->prepare("
        SELECT r.RetailerID 
        FROM Retailers r
        JOIN Users u ON r.ManagerUserID = u.UserID
        WHERE u.ApiKey = ?
    ");
    $stmt->bind_param("s", $decryptedApiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $retailerIDs = [];
    while ($row = $result->fetch_assoc()) {
        $retailerIDs[] = $row['RetailerID'];
    }
    $stmt->close();

    if (empty($retailerIDs)) {
        return $this->httpCodes("No retailers found for this manager.");
    }

    // Fetch products for those retailer IDs
    $placeholders = implode(',', array_fill(0, count($retailerIDs), '?'));
    $types = str_repeat('i', count($retailerIDs));
    $query = "
        SELECT DISTINCT
            p.ProductID, p.Name, p.Brand, p.Material, p.Description, p.Rating, p.Color,
            l.Price, l.Currency, l.Stock, pi.ImageURL
        FROM Products p
        JOIN Listings l ON p.ProductID = l.ProductID
        LEFT JOIN ProductImages pi ON p.ProductID = pi.ProductID AND pi.IsPrimary = 1
        WHERE l.RetailerID IN ($placeholders)
    ";

    $stmt = $this->connect->prepare($query);
    if (!$stmt) {
        return $this->httpCodes("Failed to prepare product query.");
    }

    $stmt->bind_param($types, ...$retailerIDs);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();

    return json_encode([
        "status" => "success",
        "data" => $products
    ]);
}



    // POST /api/manager/products
public function addProduct($managerApiKey, $data) {
    $decryptedApiKey = decryptData($managerApiKey);
    if ($decryptedApiKey === false) {
        return $this->httpCodes("Invalid API key");
    }

    $required = ['Name', 'Brand', 'Material', 'Description', 'Color', 'Price', 'Currency', 'Stock', 'ImageURL'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return $this->httpCodes("Missing field: $field");
        }
    }

    $stmt = $this->connect->prepare("
        SELECT RetailerID FROM Retailers WHERE ManagerUserID = (
            SELECT UserID FROM Users WHERE ApiKey = ?
        ) LIMIT 1
    ");
    $stmt->bind_param("s", $decryptedApiKey);
    $stmt->execute();
    $stmt->bind_result($retailerId);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found || $retailerId === null) {
        return $this->httpCodes("No retailer found for this manager.");
    }


    $stmt = $this->connect->prepare("
        SELECT ProductID FROM Products 
        WHERE Name = ? AND Brand = ? AND Material = ? AND Description = ? AND Color = ?
        LIMIT 1
    ");
    $stmt->bind_param("sssss", $data['Name'], $data['Brand'], $data['Material'], $data['Description'], $data['Color']);
    $stmt->execute();
    $stmt->bind_result($existingProductId);
    $productExists = $stmt->fetch();
    $stmt->close();

    if ($productExists && $existingProductId) {

        $stmt = $this->connect->prepare("
            SELECT ListingID FROM Listings WHERE ProductID = ? AND RetailerID = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $existingProductId, $retailerId);
        $stmt->execute();
        $stmt->bind_result($listingId);
        $listingExists = $stmt->fetch();
        $stmt->close();

        if ($listingExists) {
            return $this->httpCodes("Duplicate: Product and listing already exist.");
        }

        $productId = $existingProductId;
    } else {

        $stmt = $this->connect->prepare("
            INSERT INTO Products (Name, Brand, Material, Description, Rating, Color)
            VALUES (?, ?, ?, ?, 0, ?)
        ");
        $stmt->bind_param("sssss", $data['Name'], $data['Brand'], $data['Material'], $data['Description'], $data['Color']);
        if (!$stmt->execute()) {
            return $this->httpCodes("Failed to insert product: " . $stmt->error);
        }
        $productId = $stmt->insert_id;
        $stmt->close();
    }

    $size = $data['Size'] ?? 'M';
    $stmt = $this->connect->prepare("
        INSERT INTO Listings (ProductID, RetailerID, ProductURL, Price, LastUpdated, Size, Stock, Currency)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)
    ");
    $stmt->bind_param("iisdsss", $productId, $retailerId, $data['ImageURL'], $data['Price'], $size, $data['Stock'], $data['Currency']);
    if (!$stmt->execute()) {
        return $this->httpCodes("Failed to insert listing: " . $stmt->error);
    }
    $stmt->close();


    if (!$productExists) {
        $stmt = $this->connect->prepare("
            INSERT INTO ProductImages (ProductID, IsPrimary, ImageURL)
            VALUES (?, 1, ?)
        ");
        $stmt->bind_param("is", $productId, $data['ImageURL']);
        if (!$stmt->execute()) {
            return $this->httpCodes("Failed to add product image: " . $stmt->error);
        }
        $stmt->close();
    }


    $tags = isset($data['Tags']) ? explode(',', $data['Tags']) : [];
    foreach ($tags as $tag) {
        $cleanedTag = trim($tag);
        if ($cleanedTag === '') continue;

        $stmt = $this->connect->prepare("INSERT IGNORE INTO Tags (TagText) VALUES (?)");
        $stmt->bind_param("s", $cleanedTag);
        if (!$stmt->execute()) {
            error_log("Failed to insert tag: " . $stmt->error);
        }
        $stmt->close();

        $stmt = $this->connect->prepare("SELECT TagID FROM Tags WHERE TagText = ? LIMIT 1");
        $stmt->bind_param("s", $cleanedTag);
        $stmt->execute();
        $stmt->bind_result($tagId);
        $stmt->fetch();
        $stmt->close();

        if (!empty($tagId)) {
            $stmt = $this->connect->prepare("INSERT IGNORE INTO ProductsTags (ProductID, TagID) VALUES (?, ?)");
            $stmt->bind_param("ii", $productId, $tagId);
            if (!$stmt->execute()) {
                error_log("Failed to link product with tag: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    return json_encode([
        "status" => "success",
        "message" => $productExists ? "Listing added to existing product." : "Product, listing, and image added successfully.",
        "data" => ["ProductID" => $productId]
    ]);
}




 public function updateProduct($managerApiKey, $productId, $data) {
    if (!$this->productBelongsToManager($managerApiKey, $productId)) {
        return $this->httpCodes("Unauthorized or product not found.");
    }

    $allowed = ['Name', 'Brand', 'Material', 'Description', 'Rating', 'Color', 'Price'];
    $updates = [];
    $params = [];
    $types = '';

    foreach ($allowed as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
            $types .= ($field === 'Rating' || $field === 'Price') ? 'i' : 's';
        }
    }

    if (empty($updates)) {
        return $this->httpCodes("No fields to update.");
    }

    // Capture price update before executing the query
    $priceChanged = isset($data['Price']);
    $updatedPrice = $data['Price'] ?? null;

    $query = "UPDATE Products SET " . implode(', ', $updates) . " WHERE ProductID = ?";
    $params[] = $productId;
    $types .= 'i';

    $stmt = $this->connect->prepare($query);
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        return $this->httpCodes("Failed to update product.");
    }
    $stmt->close();


    if ($priceChanged && is_numeric($updatedPrice)) {
        $this->checkAndNotifyPriceAlerts($productId, $updatedPrice);
    }

    return json_encode([
        "status" => "success",
        "message" => "Product updated successfully"
    ]);
}



   public function deleteProduct($managerApiKey, $productId) {

    $decryptedApiKey = decryptData(trim($managerApiKey));
    if (!$decryptedApiKey) {
        return $this->httpCodes("Invalid API key.");
    }


    $stmt = $this->connect->prepare("SELECT UserID, UserType FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $decryptedApiKey);
    $stmt->execute();
    $stmt->bind_result($userId, $userType);
    $stmt->fetch();
    $stmt->close();

    if ($userType !== 'admin') {
        return $this->httpCodes("Unauthorized.");
    }


    $stmt = $this->connect->prepare("SELECT RetailerID FROM Retailers WHERE ManagerUserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($retailerId);
    $stmt->fetch();
    $stmt->close();

    if (!$retailerId) {
        return $this->httpCodes("Retailer not found.");
    }


    $stmt = $this->connect->prepare("DELETE FROM Listings WHERE ProductID = ? AND RetailerID = ?");
    $stmt->bind_param("ii", $productId, $retailerId);

    if (!$stmt->execute()) {
        return $this->httpCodes("Failed to remove product from listing.");
    }

    $stmt->close();

    return json_encode([
        "status" => "success",
        "message" => "Product unlinked from the manager's store."
    ]);
}


private function productBelongsToManager($managerApiKey, $productId) {
    $decryptedApiKey = decryptData($managerApiKey);
    if ($decryptedApiKey === false) {
        return false;
    }

    $stmt = $this->connect->prepare("
        SELECT 1 
        FROM Listings l
        JOIN Retailers r ON l.RetailerID = r.RetailerID
        JOIN Users u ON r.ManagerUserID = u.UserID
        WHERE u.ApiKey = ? AND l.ProductID = ?
        LIMIT 1
    ");
    $stmt->bind_param("si", $decryptedApiKey, $productId);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

public function getManagerPriceAlerts($data) {
    if (empty($data['api_key'])) {
        return $this->httpCodes("Missing API key.");
    }

    $decryptedApiKey = decryptData(trim($data['api_key']));
    if (!$decryptedApiKey) {
        return $this->httpCodes("Invalid API key.");
    }

    $stmt = $this->connect->prepare("SELECT UserID, UserType FROM Users WHERE ApiKey = ?");
    if (!$stmt) {
        error_log("Prepare failed (User query): " . $this->connect->error);
        return $this->httpCodes("Database error.");
    }
    $stmt->bind_param("s", $decryptedApiKey);
    if (!$stmt->execute()) {
        error_log("Execute failed (User query): " . $stmt->error);
        return $this->httpCodes("Database error.");
    }
    $stmt->bind_result($userId, $userType);
    $stmt->fetch();
    $stmt->close();

    if (!$userId) {
        return $this->httpCodes("User not found.");
    }

    if ($userType === 'customer') {
        $stmt = $this->connect->prepare("
            SELECT 
                pa.ProductID,
                pa.TargetPrice,
                pa.CreationDate,
                pa.AlertStatus,
                pa.NotificationMethod,
                p.Name AS ProductName
            FROM PriceAlerts pa
            JOIN Products p ON pa.ProductID = p.ProductID
            WHERE pa.UserID = ?
        ");
        if (!$stmt) {
            error_log("Prepare failed (PriceAlerts customer query): " . $this->connect->error);
            return $this->httpCodes("Database error.");
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            error_log("Execute failed (PriceAlerts customer query): " . $stmt->error);
            return $this->httpCodes("Database error.");
        }
        $result = $stmt->get_result();

        $alerts = [];
        while ($row = $result->fetch_assoc()) {
            $alerts[] = $row;
        }
        $stmt->close();

        return json_encode([
            "status" => "success",
            "data" => $alerts
        ]);
    }

    $stmt = $this->connect->prepare("SELECT RetailerID FROM Retailers WHERE ManagerUserID = ?");
    if (!$stmt) {
        error_log("Prepare failed (Retailers query): " . $this->connect->error);
        return $this->httpCodes("Database error.");
    }
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        error_log("Execute failed (Retailers query): " . $stmt->error);
        return $this->httpCodes("Database error.");
    }
    $result = $stmt->get_result();

    $retailerIDs = [];
    while ($row = $result->fetch_assoc()) {
        $retailerIDs[] = $row['RetailerID'];
    }
    $stmt->close();

    if (empty($retailerIDs)) {
        return $this->httpCodes("No retailers found for this manager.");
    }

    $placeholders = implode(',', array_fill(0, count($retailerIDs), '?'));
    $types = str_repeat('i', count($retailerIDs));
    $stmt = $this->connect->prepare("
        SELECT DISTINCT ProductID FROM Listings
        WHERE RetailerID IN ($placeholders)
    ");
    if (!$stmt) {
        error_log("Prepare failed (Listings query): " . $this->connect->error);
        return $this->httpCodes("Database error.");
    }

    // Bind params dynamically (with workaround for references)
    $bind_names[] = $types;
    foreach ($retailerIDs as $key => $value) {
        $bind_name = 'bind' . $key;
        $$bind_name = $value;
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    if (!$stmt->execute()) {
        error_log("Execute failed (Listings query): " . $stmt->error);
        return $this->httpCodes("Database error.");
    }
    $result = $stmt->get_result();

    $productIDs = [];
    while ($row = $result->fetch_assoc()) {
        $productIDs[] = $row['ProductID'];
    }
    $stmt->close();

    if (empty($productIDs)) {
        return $this->httpCodes("No products found for this business.");
    }

    $placeholders = implode(',', array_fill(0, count($productIDs), '?'));
    $types = str_repeat('i', count($productIDs));
    $stmt = $this->connect->prepare("
        SELECT pa.ProductID, u.Email AS UserEmail, pa.TargetPrice, p.Name AS ProductName
        FROM PriceAlerts pa
        JOIN Users u ON pa.UserID = u.UserID
        JOIN Products p ON pa.ProductID = p.ProductID
        WHERE pa.ProductID IN ($placeholders)
    ");
    if (!$stmt) {
        error_log("Prepare failed (PriceAlerts manager query): " . $this->connect->error);
        return $this->httpCodes("Database error.");
    }

    $bind_names = [];
    $bind_names[] = $types;
    foreach ($productIDs as $key => $value) {
        $bind_name = 'bindProd' . $key;
        $$bind_name = $value;
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    if (!$stmt->execute()) {
        error_log("Execute failed (PriceAlerts manager query): " . $stmt->error);
        return $this->httpCodes("Database error.");
    }
    $result = $stmt->get_result();

    $alerts = [];
    while ($row = $result->fetch_assoc()) {
        $alerts[] = $row;
    }
    $stmt->close();

    return json_encode([
        "status" => "success",
        "data" => $alerts
    ]);
}


public function checkAndNotifyPriceAlerts($productId, $newPrice) {
    

    $stmt = $this->connect->prepare("
        SELECT pa.UserID, u.Email, u.FCMToken, pa.TargetPrice, p.Name AS ProductName

        FROM PriceAlerts pa
        JOIN Users u ON pa.UserID = u.UserID
        JOIN Products p ON pa.ProductID = p.ProductID
        WHERE pa.ProductID = ? AND pa.TargetPrice >= ? AND pa.AlertStatus = 0
    ");
    $stmt->bind_param("id", $productId, $newPrice);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $email = $row['Email'];
        $target = $row['TargetPrice'];
        $token = $row['FCMToken'];
        $product = $row['ProductName'];

        $subject = "📢 Price Drop: $product now R{$newPrice}!";
        $htmlContent = "
            <html>
            <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f7f7f7;'>
                <table role='presentation' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                    <td align='center' style='padding: 20px;'>
                    <table style='max-width: 600px; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);'>
                        <tr>
                        <td>
                            <h3 style='color: #28a745; margin-top: 0;'>Good news!</h3>
                            <p style='font-size: 16px; color: #333333;'>
                            The price of <strong>$product</strong> has dropped to <strong>R$newPrice</strong>, 
                            which is at or below your alert price of <strong>R$target</strong>.
                            </p>
                            <p style='margin: 20px 0;'>
                            <a href='https://shein.com' 
                                style='display: inline-block; padding: 12px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>
                                View Product
                            </a>
                            </p>
                            <p style='font-size: 14px; color: #777777;'>
                            Thanks,<br>
                            <strong>CompareXit</strong>
                            </p>
                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>
                </table>
            </body>
            </html>
        ";

        $this->sendEmailAlert($email, $subject, $htmlContent);
        if (!empty($token)) {
        $title = "📢 $product now R{$newPrice}!";
        $body = "The price dropped below R$target";
        $data = ['productId' => $productId, 'newPrice' => "$newPrice"];
        $this->sendPushNotification($token, $title, $body, $data);
    }
    }
    $stmt->close();

    $stmt = $this->connect->prepare("
        UPDATE PriceAlerts 
        SET AlertStatus = 1 
        WHERE ProductID = ? AND TargetPrice >= ? AND AlertStatus = 0
    ");
    $stmt->bind_param("id", $productId, $newPrice);
    $stmt->execute();
    $stmt->close();
}

function sendEmailAlert($toEmail, $subject, $htmlContent) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom(EMAIL_USERNAME, 'CompareXit Alerts');

        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;

        $mail->send();
        error_log("✅ Email sent to $toEmail");
        return true;
    } catch (Exception $e) {
        error_log("❌ Email error: {$mail->ErrorInfo}");
        return false;
    }
}
private function sendPushNotification($token, $title, $body, $data = []) {
    try {
        $factory = (new Factory)->withServiceAccount('../../Assignment/configs/comparexit-bb66b-481e252f25ad.json');
        $messaging = $factory->createMessaging();

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $messaging->send($message);
        error_log("✅ Push notification sent to $token");
        return true;
    } catch (Exception $e) {
        error_log("❌ FCM error: " . $e->getMessage());
        return false;
    }
}
public function trackClick($data) {
    $apiKey = decryptData($data['apikey'] ?? '');
    $productId = intval($data['productId'] ?? 0);
    $retailerId = intval($data['retailerId'] ?? 0);

    // Get UserID from ApiKey
    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();

    if (empty($userId)) {
        return $this->httpCodes("Invalid API key");
    }

    // Insert into ProductClicks
    $stmt = $this->connect->prepare("
        INSERT INTO ProductClicks (ProductID, RetailerID, UserID, ClickTimestamp)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("iii", $productId, $retailerId, $userId);
    $stmt->execute();
    $stmt->close();

    return json_encode(["status" => "success"]);
}
public function getProductsWithImages($managerApiKey) {
    $stmt = $this->connect->prepare("
        SELECT r.RetailerID 
        FROM Retailers r
        JOIN Users u ON r.ManagerUserID = u.UserID
        WHERE u.ApiKey = ?
    ");
    $stmt->bind_param("s", $managerApiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $retailerIDs = [];
    while ($row = $result->fetch_assoc()) {
        $retailerIDs[] = $row['RetailerID'];
    }
    $stmt->close();

    if (empty($retailerIDs)) {
        return $this->httpCodes("No retailers found for this manager.");
    }

    $placeholders = implode(',', array_fill(0, count($retailerIDs), '?'));
    $types = str_repeat('i', count($retailerIDs));

    $query = "
        SELECT 
            p.ProductID, p.Name, p.Brand, p.Material, p.Description, 
            p.Rating, p.Color, pi.ImageURL, l.RetailerID, l.ProductURL
        FROM Products p
        JOIN Listings l ON p.ProductID = l.ProductID
        LEFT JOIN ProductImages pi ON pi.ProductID = p.ProductID AND pi.IsPrimary = 1
        WHERE l.RetailerID IN ($placeholders)
    ";

    $stmt = $this->connect->prepare($query);
    $stmt->bind_param($types, ...$retailerIDs);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();

    return json_encode([
        "status" => "success",
        "data" => $products
    ]);
}
public function getProducts() {
    error_log("getProducts called");

    $query = "
        SELECT 
            p.ProductID, p.Name, p.Brand, p.Material, p.Description, 
            p.Rating, p.Color, pi.ImageURL,
            l.Size, l.Stock, l.Price, l.Currency,
            r.RetailerID, r.Name AS RetailerName, r.Country, r.Type AS RetailerType,
            r.WebsiteURL, r.StoreAddress, r.IsActive
        FROM Products p
        LEFT JOIN ProductImages pi ON pi.ProductID = p.ProductID AND pi.IsPrimary = 1
        LEFT JOIN Listings l ON l.ProductID = p.ProductID
        LEFT JOIN Retailers r ON r.RetailerID = l.RetailerID
    ";

    $stmt = $this->connect->prepare($query);
    if (!$stmt) {
        return json_encode([
            "status" => "error",
            "message" => "Database error: " . $this->connect->error
        ]);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $id = $row['ProductID'];

        if (!isset($products[$id])) {
            $products[$id] = [
                "ProductID" => $row['ProductID'],
                "Name" => $row['Name'],
                "Brand" => $row['Brand'],
                "Material" => $row['Material'],
                "Description" => $row['Description'],
                "Rating" => $row['Rating'],
                "Color" => $row['Color'],
                "ImageURL" => $row['ImageURL'],
                "Listings" => []
            ];
        }

        if (!empty($row['Size']) || !empty($row['Stock'])) {
            $products[$id]["Listings"][] = [
                "Size" => $row['Size'],
                "Stock" => $row['Stock'],
                "Price" => $row['Price'],
                "Currency" => $row['Currency'],
                "Retailer" => [
                    "RetailerID" => $row['RetailerID'],
                    "Name" => $row['RetailerName'],
                    "Country" => $row['Country'],
                    "Type" => $row['RetailerType'],
                    "WebsiteURL" => $row['WebsiteURL'],
                    "StoreAddress" => $row['StoreAddress'],
                    "IsActive" => $row['IsActive']
                ]
            ];
        }
    }


    $stmt->close();
    return json_encode([
        "status" => "success",
        "data" => array_values($products)
    ]);
}

public function getManagerProducts($apiKey) {
    if (empty($apiKey)) {
        return $this->httpCodes("Missing API key.");
    }

    $decryptedApiKey = decryptData(($apiKey));
    if (!$decryptedApiKey) {
        return $this->httpCodes("Invalid API key.");
    }


    $stmt = $this->connect->prepare("SELECT UserID, UserType FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $decryptedApiKey);
    $stmt->execute();
    $stmt->bind_result($userId, $userType);
    $stmt->fetch();
    $stmt->close();

    if ($userType !== 'admin') {
        return $this->httpCodes("Unauthorized.");
    }


    $stmt = $this->connect->prepare("SELECT RetailerID FROM Retailers WHERE ManagerUserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $retailerIDs = [];
    while ($row = $result->fetch_assoc()) {
        $retailerIDs[] = $row['RetailerID'];
    }
    $stmt->close();

    if (empty($retailerIDs)) {
        return $this->httpCodes("No retailers found for this manager.");
    }


    $placeholders = implode(',', array_fill(0, count($retailerIDs), '?'));
    $types = str_repeat('i', count($retailerIDs));


    $stmt = $this->connect->prepare("SELECT DISTINCT ProductID FROM Listings WHERE RetailerID IN ($placeholders)");
    $stmt->bind_param($types, ...$retailerIDs);
    $stmt->execute();
    $result = $stmt->get_result();

    $productIDs = [];
    while ($row = $result->fetch_assoc()) {
        $productIDs[] = $row['ProductID'];
    }
    $stmt->close();

    if (empty($productIDs)) {
        return $this->httpCodes("No products found for this business.");
    }


    $placeholders = implode(',', array_fill(0, count($productIDs), '?'));
    $types = str_repeat('i', count($productIDs));

$sql = "
    SELECT 
        p.ProductID,
        p.Name,
        p.Brand,
        p.Material,
        p.Description,
        p.Rating,
        p.Color,
        (
            SELECT l.Price
            FROM Listings l
            WHERE l.ProductID = p.ProductID
            LIMIT 1
        ) AS Price,
        (
            SELECT l.Stock
            FROM Listings l
            WHERE l.ProductID = p.ProductID
            LIMIT 1
        ) AS Stock,
        (
            SELECT pi.ImageURL
            FROM ProductImages pi
            WHERE pi.ProductID = p.ProductID
            LIMIT 1
        ) AS ImageURL
    FROM Products p
    WHERE p.ProductID IN ($placeholders)
";




    $stmt = $this->connect->prepare($sql);
    $stmt->bind_param($types, ...$productIDs);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();

    return json_encode([
        "status" => "success",
        "data" => $products
    ]);
}

public function fill(){
     $email= 'melissa.wilson93@gmail.com';
    


     $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    die("Manager email not found.");
}


$stmt = $this->connect->prepare("SELECT RetailerID FROM Retailers WHERE ManagerUserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($retailerId);
$stmt->fetch();
$stmt->close();

if (!$retailerId) {
    die("Retailer not found.");
}


$sql = "
SELECT l.ProductID, l.ProductURL, l.Price, l.Size, l.Stock, l.Currency
FROM Listings l
LEFT JOIN Listings existing 
    ON existing.ProductID = l.ProductID AND existing.RetailerID = ?
WHERE existing.ProductID IS NULL
LIMIT 100
";
$stmt = $this->connect->prepare($sql);
$stmt->bind_param("i", $retailerId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("No new products to insert.");
}


$insert = $this->connect->prepare("INSERT IGNORE INTO Listings (ProductID, RetailerID, ProductURL, Price, LastUpdated, Size, Stock, Currency) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)");

while ($row = $result->fetch_assoc()) {
    $insert->bind_param(
        "iisdsss",
        $row['ProductID'],
        $retailerId,
        $row['ProductURL'],
        $row['Price'],
        $row['Size'],
        $row['Stock'],
        $row['Currency']
    );
    $insert->execute();
}

$insert->close();
echo "Inserted new products successfully.";
}
public function getClickAnalytics($apiKey) {
    $stmt = $this->connect->prepare("
        SELECT DATE(ClickTimestamp) as ClickDate, COUNT(*) as TotalClicks
        FROM ProductClicks
        WHERE UserID = (SELECT UserID FROM Users WHERE ApiKey = ?)
        GROUP BY ClickDate
        ORDER BY ClickDate ASC
    ");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
    error_log("Query failed: " . $this->connect->error);
} else {
    error_log("Query succeeded, rows: " . $result->num_rows);
}


    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        error_log("Row data: " . print_r($row, true));
    $labels[] = $row['ClickDate'];
    $data[] = intval($row['TotalClicks']);
    error_log("How many: " . $row['TotalClicks']);
}
$stmt->close();

    return json_encode([
        "status" => "success",
        "labels" => $labels,
        "data" => $data
    ]);
}
public function fetchProfileInfo($apiKey) {
$stmt = $this->connect->prepare("SELECT Email, UserType, (SELECT Name FROM Retailers WHERE UserID = Users.UserID LIMIT 1) as BusinessName FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $stmt->bind_result($email, $userType, $businessName);
    if ($stmt->fetch()) {//1506
        echo json_encode([
            'success' => true,
            'email' => $email,
            'role' => $userType,
            'businessName' => $businessName
        ]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
    $stmt->close();
}

public function updateBusinessName($apiKey, $newName) {
    if (strlen(trim($newName)) < 3) {
        return ['error' => 'Business name too short'];
    }

    $check = $this->connect->prepare("SELECT 1 FROM Retailers WHERE Name = ?");
    $check->bind_param("s", $newName);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        return ['error' => 'Business name already exists, try another name.'];
    }

        $apiKey = decryptData($apiKey);

    $userId = $this->getUserIdFromApiKey($apiKey);
    if (!$userId) {
        return ['error' => 'Invalid user'];
    }

    $update = $this->connect->prepare("UPDATE Retailers SET Name = ? WHERE ManagerUserID = ?");
    $update->bind_param("si", $newName, $userId);
    if ($update->execute()) {
        return ['success' => true];
    } else {
        return ['error' => 'An error occurred, try again later'];
    }
}


function changePassword($apiKey, $newPassword) {
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $newPassword)) {
        echo json_encode(['error' => 'Weak password']);
        return;
    }

    $userId = $this->getUserIdFromApiKey($apiKey);
    if (!$userId) {
        echo json_encode(['error' => 'Invalid session']);
        return;
    }

    $salt1 = bin2hex(random_bytes(16));
    $salt2 = bin2hex(random_bytes(8));
    $hash = password_hash($salt2 . $salt1 . $newPassword, PASSWORD_ARGON2ID);

    $stmt = $this->connect->prepare("UPDATE Users SET salt1 = ?, salt2 = ?, PasswordHash = ? WHERE UserID = ?");
    $stmt->bind_param("sssi", $salt1, $salt2, $hash, $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Password change failed']);
    }

    $stmt->close();
}



function getUserIdFromApiKey($apiKey) {

    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $stmt->bind_result($userId);
    return $stmt->fetch() ? $userId : false;
}

public function getProductByID($data) {
    error_log("I have been called");
    $id = $data['id'];

    $query = "
        SELECT 
            p.ProductID, p.Name, p.Brand, p.Material, p.Description, 
            p.Rating, p.Color, pi.ImageURL,
            l.Size, l.Stock, l.Price, l.Currency,
            r.RetailerID, r.Name AS RetailerName, r.Country, r.Type AS RetailerType,
            r.WebsiteURL, r.StoreAddress, r.IsActive
        FROM Products p
        LEFT JOIN ProductImages pi ON pi.ProductID = p.ProductID AND pi.IsPrimary = 1
        LEFT JOIN Listings l ON l.ProductID = p.ProductID
        LEFT JOIN Retailers r ON r.RetailerID = l.RetailerID 
        WHERE p.ProductID = ".$this->RemoveSpecialChar($id);

    $stmt = $this->connect->prepare($query);
    if (!$stmt) {
        return json_encode([
            "status" => "error",
            "message" => "Database error: " . $this->connect->error
        ]);
    }

    $stmt->execute();
    $result = $stmt->get_result();


    $products = [];

    while ($row = $result->fetch_assoc()) {
        $id = $row['ProductID'];

        if (!isset($products[$id])) {
            $products[$id] = [
                "ProductID" => $row['ProductID'],
                "Name" => $row['Name'],
                "Brand" => $row['Brand'],
                "Material" => $row['Material'],
                "Description" => $row['Description'],
                "Rating" => $row['Rating'],
                "Color" => $row['Color'],
                "ImageURL" => $row['ImageURL'],
                "Listings" => []
            ];
        }

        if (!empty($row['Size']) || !empty($row['Stock'])) {
    $products[$id]["Listings"][] = [
        "Size" => $row['Size'],
        "Stock" => $row['Stock'],
        "Price" => $row['Price'],
        "Currency" => $row['Currency'],
        "Retailer" => [
            "RetailerID" => $row['RetailerID'],
            "Name" => $row['RetailerName'],
            "Country" => $row['Country'],
            "Type" => $row['RetailerType'],
            "WebsiteURL" => $row['WebsiteURL'],
            "StoreAddress" => $row['StoreAddress'],
            "IsActive" => $row['IsActive']
        ]
    ];
}

    }

    $stmt->close();

    return json_encode([
        "status" => "success",
        "data" => array_values($products)
    ]);
    }
    public   function RemoveSpecialChar($str) {

      $resulting = str_replace( array( '\'', '"',
      ',' , ';', '<', '>' ), ' ', $str);

      return $resulting;
    }

function handleCreateReview($data)
{
    $timestamp = round(microtime(true) * 1000);

    if (!isset($data['api_key'])) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "timestamp" => $timestamp,
            "message" => "Missing API key"
        ]);
        exit;
    }


    $apiKey = decryptData($data['api_key']);
    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "timestamp" => $timestamp,
            "message" => "Failed to prepare user lookup"
        ]);
        exit;
    }

    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $stmt->bind_result($userId);

    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode([
            "status" => "error",
            "timestamp" => $timestamp,
            "message" => "Invalid API key"
        ]);
        exit;
    }
    $stmt->close();

    $data['user_id'] = $userId;

    $required = ['user_id', 'product_id', 'rating', 'review_date', 'review_text'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "timestamp" => $timestamp,
                "message" => "Missing field: " . $field
            ]);
            exit;
        }
    }

    try {
        $comment = $data['comment'] ?? null;


        $stmt = $this->connect->prepare("
            INSERT INTO Reviews (UserID, ProductID, Rating, ReviewDate, ReviewText)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              Rating = VALUES(Rating),
              ReviewDate = VALUES(ReviewDate),
              ReviewText = VALUES(ReviewText)
        ");
        if (!$stmt) throw new Exception("Database prepare failed");

        $stmt->bind_param(
            "iiiss",
            $data['user_id'],
            $data['product_id'],
            $data['rating'],
            $data['review_date'],
            $data['review_text']
        );

        $stmt->execute();


        echo json_encode([
            "status" => "success",
            "timestamp" => $timestamp,
            "data" => [
                "user_id" => $data['user_id'],
                "product_id" => $data['product_id'],
                "rating" => $data['rating'],
                "comment" => $comment
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "timestamp" => $timestamp,
            "message" => $e->getMessage()
        ]);
    }

    exit;
}
function handleGetReviews($data)
{
    $timestamp = round(microtime(true) * 1000);

    if (!isset($data['product_id'])) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "timestamp" => $timestamp,
            "message" => "Missing product_id"
        ]);
        exit;
    }

    $productId = $data['product_id'];

    try {
        $stmt = $this->connect->prepare("
            SELECT 
                R.Rating,
                R.ReviewText,
                R.ReviewDate,
                U.FirstName,
                U.LastName
            FROM Reviews R
            JOIN Users U ON R.UserID = U.UserID
            WHERE R.ProductID = ?
            ORDER BY R.ReviewDate DESC
        ");

        if (!$stmt) {
            throw new Exception("Failed to prepare review query");
        }

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'rating' => (int)$row['Rating'],
                'text' => $row['ReviewText'],
                'date' => $row['ReviewDate'],
                'reviewer' => $row['FirstName'] . ' ' . strtoupper(substr($row['LastName'], 0, 1)) . '.'
            ];
        }

        echo json_encode([
            "status" => "success",
            "timestamp" => $timestamp,
            "reviews" => $reviews
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "timestamp" => $timestamp,
            "message" => $e->getMessage()
        ]);
    }

    exit;
}


  public function wishlist($data){
        $productId = $data['product_id'];
    $desiredPrice = $data['desired_price'] ?? null;
    $apiKey = decryptData($data['api_key']) ?? null;

    if (!$apiKey || !$productId || !$desiredPrice) {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        exit;
    }

    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
        exit;
    }

    $userId = $user['id'];


    $stmt = $this->connect->prepare("SELECT * FROM Wishlists WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'Already in wishlist']);
        exit;
    }


    $stmt = $this->connect->prepare("INSERT INTO Wishlists (UserID, ProductID, DateAdded, DesiredPrice) VALUES (?, ?, CURDATE(), ?)");
    $stmt->bind_param("iid", $userId, $productId, $desiredPrice);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB insert failed']);
    }
    }
public function wishlistDel($data){
   
    error_log("wishlistDel was called!");

    $productId = $data['product_id'];
    $apiKey = decryptData($data['api_key']) ?? null;

    if (!$productId || !$apiKey) {
        echo json_encode(["status" => "error", "message" => "Missing data"]);
        exit;
    }

    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "Invalid API key"]);
        exit;
    }

    $userId = $user['id'];

    $query = "DELETE FROM Wishlists
 WHERE ProductID = ? AND UserID = ?";
    $stmt = $this->connect->prepare($query);

    if (!$stmt) {
        error_log("From log: {$connect->error}");
        echo json_encode(["status" => "error", "message" => "Prepare failed"]);
        exit;
    }

    $stmt->bind_param("ii", $productId, $userId);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "DB error"]);
        error_log("Delete failed");
    } else {
        echo json_encode(["status" => "success"]);
    }

    

}
public function wishlistDisplay($data) {

    
    $apiKey = decryptData($data['api_key']) ?? null;

    if (!$apiKey) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }


    $stmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
        exit;
    }

    $userId = $user['UserID'];


    $stmt = $this->connect->prepare("SELECT ProductID FROM Wishlists WHERE UserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $idArray = [];
    while ($row = $result->fetch_assoc()) {
        $idArray[] = $row['ProductID'];
    }

    if (empty($idArray)) {
        echo json_encode(['status' => 'success', 'data' => []]);
        exit;
    }


    $placeholders = implode(',', array_fill(0, count($idArray), '?'));
    $types = str_repeat('i', count($idArray));


    $query = "
        SELECT 
            p.ProductID, p.Name, p.Brand, p.Description, p.Rating, 
            pi.ImageURL AS image_url 
        FROM Products p 
        LEFT JOIN ProductImages pi ON p.ProductID = pi.ProductID AND pi.IsPrimary = 1 
        WHERE p.ProductID IN ($placeholders)
    ";

    $stmt = $this->connect->prepare($query);
    $stmt->bind_param($types, ...$idArray);
    $stmt->execute();
    $result = $stmt->get_result();

    $wishlistData = [];
    while ($row = $result->fetch_assoc()) {
        $wishlistData[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $wishlistData]);
}
function handleGetRecommendations($data) 
{
    $timestamp = round(microtime(true) * 1000);


    if (!isset($data['api_key']))
    {
        http_response_code(400);
        echo json_encode(["status" => "error", "timestamp" => $timestamp, "message" => "API key is required"]);
        exit;
    }


    $decryptedApiKey = decryptData($data['api_key']);

    try
    {

        $userStmt = $this->connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
        if (!$userStmt) throw new Exception("Database prepare failed (User lookup)");

        $userStmt->bind_param("s", $decryptedApiKey);
        $userStmt->execute();
        $userResult = $userStmt->get_result();

        if ($userResult->num_rows === 0)
        {
            http_response_code(401);
            echo json_encode(["status" => "error", "timestamp" => $timestamp, "message" => "Invalid API key"]);
            exit;
        }

        $userRow = $userResult->fetch_assoc();
        $userID = $userRow['UserID'];


        $stmt = $this->connect->prepare("
            SELECT 
                p.ProductID, 
                p.Name, 
                p.Description, 
                p.Material, 
                p.Rating, 
                p.Color, 
                p.Brand,
                pi.ImageURL
            FROM Products p
            LEFT JOIN ProductImages pi 
                ON p.ProductID = pi.ProductID AND pi.IsPrimary = 1
            ORDER BY RAND()
            LIMIT 5
        ");
        if (!$stmt) throw new Exception("Database prepare failed (Product lookup)");

        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            "status" => "success", 
            "timestamp" => $timestamp, 
            "user_id" => $userID,
            "data" => $products
        ]);
    }
    catch (Exception $e)
    {
        http_response_code(500);
        echo json_encode([
            "status" => "error", 
            "timestamp" => $timestamp, 
            "message" => $e->getMessage()
        ]);
    }

    exit;
}


}
    
function getRequestBody() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        error_log("API JSON received: " . json_encode($data));

        if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}


        return $data;
    }


    return $_POST;
}


$userAPI = UserAPI::getter($connect);


$data = getRequestBody();
$type = $data['type'] ?? '';

switch ($type) {
    case 'Fill':
                echo $userAPI->fill();

        exit;
    case 'Register':
        $data = getRequestBody();
        echo $userAPI->registerUser($data);
        exit;

    case 'Login':
        $data = getRequestBody();
        echo $userAPI->loginUser($data);
        exit;
    case 'GetAllProducts':
        $data = getRequestBody();
         //echo $userAPI->fill();
        echo $userAPI->getProducts();

        exit;
    case 'getProductByID':
        $data = getRequestBody();
         //echo $userAPI->fill();
        echo $userAPI->getProductByID($data);

        exit;

    

    case 'RegisterBusiness':
        $data = getRequestBody();
        echo $userAPI->registerBusiness($data);
        exit;
    case 'getManagerPriceAlerts':
        $data = getRequestBody();
        echo $userAPI->getManagerPriceAlerts($data);
        exit;
        
        case 'addProductManager':
        $data = getRequestBody();
        echo $userAPI->addProduct($data['api_key'],$data['data']);
        exit;

    
        case 'getAllProductsManager':
            $data = getRequestBody();
        echo $userAPI->getManagerProducts($data['api_key']);
            exit;
    case 'removeProduct':
    $data = getRequestBody();
    echo $userAPI->deleteProduct($data['api_key'], $data['productId']);
    exit;
    case 'fetchProfileInfo':
        $data = getRequestBody();
        echo $userAPI->fetchProfileInfo(decryptData($data['api_key']) ?? '');
        break;
    case 'updateBusinessName':
    $input = getRequestBody();
    $result = $userAPI->updateBusinessName($input['api_key'] ?? '', $input['businessName'] ?? '');
    echo json_encode($result);
    break;

    case 'changePassword':
                $data = getRequestBody();

        echo $userAPI->changePassword(decryptData($data['apiKey']) ?? '', $data['newPassword'] ?? '');
        break;
        case 'handleCreateReview':
                $data = getRequestBody();

         $userAPI->handleCreateReview($data);
        break;
        

    case 'GetClickAnalytics':
    $data = getRequestBody();
    $decryptedApiKey = decryptData($data['api_key']);
    echo $userAPI->getClickAnalytics($decryptedApiKey);
    break;

    case 'Delete':
        $data = getRequestBody();

        echo $userAPI->deleteUser($data);

        exit;

        case 'handleGetReviews':
            $data = getRequestBody();

            echo $userAPI->handleGetReviews($data);
            ;
case 'wishlist':
    $data = getRequestBody();
    echo $userAPI->wishlist($data);
    break;

case 'wishlistDel':
    $data = getRequestBody();
    echo $userAPI->wishlistDel($data);
    break;
    case 'wishlistDisplay':
        $data = getRequestBody();
    echo $userAPI->wishlistDisplay($data);
        break;

      case  'handleGetRecommendations':
         $data = getRequestBody();
    echo $userAPI->handleGetRecommendations($data);
        break;
        exit;



    default:
        http_response_code(400);
        echo json_encode(["error" => "Unknown request type: $type"]);
        exit;
}
    


?>


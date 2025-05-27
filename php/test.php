<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
require_once '../../../Assignment/configs/email_credentials.php';

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME;
$mail->Password = EMAIL_PASSWORD; 
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Email headers
    $mail->setFrom(EMAIL_USERNAME, 'CompareXit Alerts');
    $mail->addAddress('zelamene897@gmail.com'); 

    $mail->isHTML(true);
    $mail->Subject = '📢 Test Email from CompareXit';
    $mail->Body    = '
        <html>
            <body>
                <h2 style="color: green;">Success!</h2>
                <p>This is a test email to verify PHPMailer setup.</p>
            </body>
        </html>
    ';

    $mail->send();
    echo "✅ Email sent successfully.";
} catch (Exception $e) {
    echo "❌ Email failed: {$mail->ErrorInfo}";
}
case 'wishlist':
    $productId = $data['product_id'];
    $desiredPrice = $data['desired_price'] ?? null;
    $apiKey = decryptData($data['api_key']) ?? null;

    if (!$apiKey || !$productId || !$desiredPrice) {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        exit;
    }

    $stmt = $connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
        exit;
    }

    $userId = $user['id'];


    $stmt = $connect->prepare("SELECT * FROM Wishlists WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'Already in wishlist']);
        exit;
    }


    $stmt = $connect->prepare("INSERT INTO Wishlists (UserID, ProductID, DateAdded, DesiredPrice) VALUES (?, ?, CURDATE(), ?)");
    $stmt->bind_param("iid", $userId, $productId, $desiredPrice);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB insert failed']);
    }

    exit;
    public function wishlist($data){
        $productId = $data['product_id'];
    $desiredPrice = $data['desired_price'] ?? null;
    $apiKey = decryptData($data['api_key']) ?? null;

    if (!$apiKey || !$productId || !$desiredPrice) {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        exit;
    }

    $stmt = $connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
        exit;
    }

    $userId = $user['id'];


    $stmt = $connect->prepare("SELECT * FROM Wishlists WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'Already in wishlist']);
        exit;
    }


    $stmt = $connect->prepare("INSERT INTO Wishlists (UserID, ProductID, DateAdded, DesiredPrice) VALUES (?, ?, CURDATE(), ?)");
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

    $stmt = $connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
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
    $stmt = $connect->prepare($query);

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
 case 'wishlistDel':
    exit;

 public  function wishlistDisplay($data){
    $apiKey = decryptData($data['api_key']) ?? null;

    if (!$apiKey) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }

    $stmt = $connect->prepare("SELECT UserID FROM Users WHERE ApiKey = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
        exit;
    }

    $userId = $user['id'];

    $stmt = $connect->prepare("SELECT ProductID FROM Wishlists WHERE UserID = ?");
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
    $query = "SELECT id, final_price, title, image_url FROM products WHERE id IN ($placeholders)";
    $stmt = $connect->prepare($query);
    $stmt->bind_param($types, ...$idArray);
    $stmt->execute();
    $result = $stmt->get_result();

    $wishlistData = [];
    while ($row = $result->fetch_assoc()) {
        $wishlistData[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $wishlistData]);
}
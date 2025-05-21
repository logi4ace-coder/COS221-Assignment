<?php

session_start();

require_once("COS221/Ass5/php/config.php"); 

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
        error_log("registerUser called with: " . json_encode($data));

        $missing_data = [];
        $required_fields = ['type', 'name', 'surname', 'email', 'password', 'user_type'];

        foreach ($required_fields as $parameter) {
            if (empty($data[$parameter])) {
                $missing_data[] = $parameter;
            }
        }

        if (!empty($missing_data)) {
            error_log("Missing fields: " . implode(", ", $missing_data));
            echo $this->httpCodes("<br>Missing fields: " . implode(", ", $missing_data));
            return;
        }

        if ($data['type'] !== 'Register') {
            error_log("Invalid type parameter: " . $data['type']);
            return $this->httpCodes("Invalid request type");
        }

        $name = trim($data['name']);
        $surname = trim($data['surname']);
        $email = trim($data['email']);
        $password = $data['password'];
        $user_type = $data['user_type'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid email: $email");
            return $this->httpCodes("Invalid email format");
        }

        $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
        if (!preg_match($password_regex, $password)) {
            error_log("Password wrong!");
            return $this->httpCodes("Password must be at least 8 characters long, contain upper and lowercase letters, at least one digit, and one special character.");
        }

        if ($this->emailDuplicationCheck($email)) {
            error_log("Email already registered: $email");
            return $this->httpCodes("Email already registered. Try another one.");
        }

        $salt1 = bin2hex(random_bytes(16));
        $salt2 = bin2hex(random_bytes(8));
        $hashed_password = password_hash($salt2 . $salt1 . $password, PASSWORD_ARGON2ID);
        $api_key = bin2hex(random_bytes(16));

        if ($this->insertUser($name, $surname, $email, $hashed_password, $user_type, $api_key, $salt1, $salt2)) {
            error_log("User successfully inserted into the database: $email");

            $query = "INSERT INTO Themes (api_key) values (?)";
            $stmt = $this->connect->prepare($query);
            $stmt->bind_param("s", $api_key);
            $stmt->execute();
            $stmt->close();

            return $this->successJson($api_key);
        } else {
            error_log("Failed to insert user: $email");
            return $this->httpCodes("Error inserting user {$name} into database");
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
            (FirstName, LastName, Email, PasswordHash, UserType, api_key, salt1, salt2)
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
        echo json_encode(['status' => 'error', 'message' => $message]);
        $_SESSION['errors'] = ['validation' => [$message]];
        header("Location: COS221/Assignment/../login.php");
        exit;
    }

    public function loginUser($data) {
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $secretKey = "6LeTSjYrAAAAAKqd_rfuQ6CxS7-Iu5pOO7_NHMfL";
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
                $errors['password'] = "Password must be at least 8 characters, include upper/lowercase, number, and special char.";
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

        $stmt = $this->connect->prepare("SELECT api_key, UserType FROM Users WHERE Email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($apiKey, $userType);
        $stmt->fetch();
        $stmt->close();

        $_SESSION['api_key'] = $apiKey;
        header("Location: COS221/Assignment/../index.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

	$NON_POST = "Invalid Request method {$_SERVER['REQUEST_METHOD']}";

	error_log($NON_POST);
	http_response_code(405);
	echo json_encode(["status" => "error", "message" => $NON_POST]);

	exit;
}

else{

    $body = file_get_contents("php://input");
    $IS_JSON = $_SERVER['CONTENT_TYPE'] ?? '';

    if (strpos($IS_JSON, 'application/json') !== false) {

        $data = json_decode(file_get_contents('php://input'), true);
        error_log("Api info recived: " . json_encode($data));

    } 
    else {

        $data = $_POST;

    }
        error_log("Info recieed: " . json_encode($data));

    if (!$data || !isset($data['type'])) {
        echo json_encode(["status" => "error", "message" => "Missing or invalid 'type' {$data['type']}"]);
        exit;

    }

    $type = $data['type'];



    error_log("Request Body: " . $body);
    $userAPI = UserAPI::getter($connect);

    
    switch ($type) {

    case 'Register':

        echo $userAPI->registerUser($data);

        break;
    
    case 'Login':
        echo $userAPI->loginUser($data);    
        break;

   
}
 
}
?>

<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'your_db_user';
$pass = 'your_db_password';
$dbname = 'your_db_name';
try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// CSRF token generation
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Login handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email
    if (!isValidEmail($email)) {
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    // Prepare statement to prevent SQL injection
    $stmt = $dbh->prepare('SELECT id, password FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set secure session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $email;
        echo json_encode(['success' => 'Login successful']);
    } else {
        echo json_encode(['error' => 'Invalid email or password']);
    }
}

// Generate CSRF token for the form
$csrf_token = generateCsrfToken();
?>

<!-- HTML part for login form -->
<form method="POST" action="login.php">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
</form>
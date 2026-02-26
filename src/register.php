<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$host = 'localhost';
$db   = 'marikina_db';          
$user = 'marikina_user';
$pass = 'marikina_password';                     

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = 'Veterinarian';  

    if (empty($username) || empty($full_name) || empty($password)) {
        $message = "All fields are required.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $full_name, $hashed_password, $role);

        if ($stmt->execute()) {
    $message = "Account created successfully! <a href='login.php' style='color:#F09797; font-weight: 700;'>Sign in here</a>";
} else {
    
    if ($conn->errno == 1062) {  
        $message = "Username <strong>" . htmlspecialchars($username) . "</strong> is already taken. Please use a different username.";
    } else {
        $message = "Registration error: " . $stmt->error;
    }
}
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register – Marikina Animal & Welfare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

  <div class="register-container">
    <div class="register-left">
      <div class="logo-icon">🐾</div>
      <h1>Marikina Animal & Welfare</h1>
      <p>Join our community of animal lovers and advocates</p>
    </div>

    <div class="register-right">
      <div class="register-header">
        <h2>Create Account</h2>
        <p>Join us to help the animals</p>
      </div>

      <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Choose a username" required autofocus>
        </div>

        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Create a password" required>
        </div>

        <button type="submit">Sign Up</button>
      </form>

      <div class="login-link">
        Already have an account? <a href="login.php">Sign in</a>
      </div>
    </div>
  </div>

</body>
</html>
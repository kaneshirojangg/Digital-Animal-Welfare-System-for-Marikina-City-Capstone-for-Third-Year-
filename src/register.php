<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$host = 'localhost';
$db   = 'marikina_db';          
$user = 'root';
$pass = '';                     

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
        $message = "Lahat ng field ay kailangan punan.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $full_name, $hashed_password, $role);

        if ($stmt->execute()) {
    $message = "Account created successfully! <a href='login.php' style='color:#2c7d4e;'>Mag-login na dito</a>";
} else {
    
    if ($conn->errno == 1062) {  
        $message = "Username <strong>" . htmlspecialchars($username) . "</strong> ay ginagamit na. Gumamit ng ibang username.";
    } else {
        $message = "May error sa pagrehistro: " . $stmt->error;
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f9f4 0%, #e6f4ea 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      margin: 0;
    }
    .register-box {
      background: white;
      padding: 50px 40px;
      border-radius: 16px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 450px;
      border: 1px solid #e2e8f0;
    }
    h2 {
      text-align: center;
      color: #2c7d4e;
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      margin-bottom: 24px;
    }
    .message {
      text-align: center;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    .success { background: #dcfce7; color: #166534; }
    .error   { background: #fee2e2; color: #dc2626; }
    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    input {
      padding: 14px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 1rem;
    }
    input:focus {
      outline: none;
      border-color: #2c7d4e;
      box-shadow: 0 0 0 3px rgba(44,125,78,0.2);
    }
    button {
      background: #2c7d4e;
      color: white;
      border: none;
      padding: 16px;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #1e5c38;
    }
    .login-link {
      text-align: center;
      margin-top: 24px;
      color: #4b5563;
    }
    .login-link a {
      color: #2c7d4e;
      font-weight: 600;
      text-decoration: none;
    }
    .login-link a:hover {
      text-decoration: underline;
    }
    /* Para sa test message */
    .test-msg {
      text-align: center;
      color: #6b7280;
      margin-bottom: 20px;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

  <div class="register-box">
    <h2>Create Account</h2>

    <div class="test-msg">Register Page Test - PHP is running</div>

    <?php if ($message): ?>
      <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" placeholder="Username" required autofocus>
      <input type="text" name="full_name" placeholder="Full Name" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
    </form>

    <div class="login-link">
      May account na? <a href="login.php">Mag-login</a>
    </div>
  </div>

</body>
</html>
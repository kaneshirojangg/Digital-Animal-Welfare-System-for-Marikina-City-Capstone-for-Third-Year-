<?php
include 'session-handler.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
	$username = trim($_POST['username'] ?? '');
	$password = trim($_POST['password'] ?? '');

	if (empty($username) || empty($password)) {
		$error = "Please enter both username and password.";
	} else {

		$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');

		if ($conn->connect_error) {
			$error = "Database connection error: " . $conn->connect_error;
		} else {

			$stmt = $conn->prepare("SELECT id, password, full_name FROM users WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result && $result->num_rows > 0) {
				$user = $result->fetch_assoc();

				if (password_verify($password, $user['password'])) {

				session_regenerate_id(true);

					$_SESSION['loggedIn'] = true;
					$_SESSION['username'] = $username;
					$_SESSION['user_id'] = $user['id'];
					$_SESSION['full_name'] = $user['full_name'];

					header("Location: dashboard.php");
					exit();
				} else {
					$error = "Invalid password. Please try again.";
				}
			} else {
				$error = "Username not found. Please check your username.";
			}

			$stmt->close();
			$conn->close();
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login – Marikina Animal & Welfare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

  <div class="login-container">
    <div class="login-left">
      <a href="index.php" class="logo-icon">
        <img src="../assets/images/pet.jpg" alt="Back to Home">
      </a>
      <h1>Marikina Animal & Welfare</h1>
      <p>Help us provide better care for the animals in our community</p>
    </div>

    <div class="login-right">
      <div class="logo-title">
        <h2>Sign In</h2>
        <p>Sign in to continue</p>
      </div>

      <?php if ($error): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success-message">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="form-group">
          <label for="username">Username or Email</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="forgot-password">
          <a href="#">I can't remember my password</a>
        </div>

        <button type="submit" name="login" class="btn">Sign in</button>
      </form>

      <div class="signup-prompt">
        Don't have an account? <a href="register.php">Sign up</a>
      </div>
    </div>
  </div>

</body>
</html>
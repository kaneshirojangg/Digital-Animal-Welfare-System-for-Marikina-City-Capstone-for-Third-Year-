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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --text: #2d3748;
      --bg: #f8fafc;
      --card: #ffffff;
      --border: #e2e8f0;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f0f9f4 0%, #e6f4ea 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-box {
      background: white;
      padding: 40px 36px;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.12);
      width: 100%;
      max-width: 420px;
      border: 1px solid var(--border);
    }

    .logo-title {
      text-align: center;
      margin-bottom: 32px;
    }

    .logo-title h1 {
      font-family: 'Playfair Display', serif;
      color: var(--primary-dark);
      font-size: 1.9rem;
      margin-bottom: 8px;
    }

    h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 28px;
      font-size: 1.5rem;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #475569;
    }

    input {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 1rem;
    }

    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(44,125,78,0.15);
    }

    .btn {
      width: 100%;
      padding: 14px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.05rem;
      font-weight: 600;
      cursor: pointer;
    }

    .btn:hover {
      background: var(--primary-dark);
    }

    .back-link {
      text-align: center;
      margin-top: 24px;
      font-size: 0.9rem;
    }

    .back-link a {
      color: var(--primary);
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <div class="logo-title">
      <h1>Marikina Animal & Welfare</h1>
      <p>Management System</p>
    </div>

    <h2>Sign In</h2>

    <?php if ($error): ?>
      <div style="background: #fee; color: #c00; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div style="background: #efe; color: #060; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem;">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" placeholder="Enter username or email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required>
      </div>

      <button type="submit" name="login" class="btn">Login</button>
    </form>

    <div class="back-link">
      <a href="index.php">← Back to Public Page</a>
    </div>
  </div>

</body>
</html>
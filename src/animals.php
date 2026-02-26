<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

$activePage = 'animals';

$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_animal'])) {
    $name       = trim($_POST['name'] ?? '');
    $type       = trim($_POST['type'] ?? '');
    $age        = trim($_POST['age'] ?? '');
    $gender     = trim($_POST['gender'] ?? '');
    $status     = trim($_POST['status'] ?? 'In Shelter');
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || empty($type)) {
        $error = "Kailangan punan ang Name at Type.";
    } else {
        $stmt = $conn->prepare("INSERT INTO animals (name, type, age, gender, status, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $name, $type, $age, $gender, $status, $description);

        if ($stmt->execute()) {
            $success = "Bagong animal record na naidagdag!";
        } else {
            $error = "Error sa pag-save: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT id, name, type, age, gender, status, intake_date FROM animals ORDER BY intake_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
		<meta charset="UTF-8">
		<title>Animals - Marikina A&W</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="../assets/css/variables.css">
		<link rel="stylesheet" href="../assets/css/nav.css">
		<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php include 'nav-menu.php'; ?>

<div class="topbar">
  <div class="logo"><h1>Marikina A&W</h1></div>
  <button class="exit-btn" onclick="if(confirm('Sigurado ka bang lalabas?')) window.location.href='logout.php'">
    Exit / Logout
  </button>
</div>

<div class="content">
  <h1>Animals Management</h1>

  <!-- ✅ CENTERED TEXT -->
  <p class="subtitle">
    Dito mo ilalagay ang listahan ng mga hayop, form para mag-add ng bagong animal, search, edit, delete, etc.
  </p>

  <button class="add-btn" onclick="document.getElementById('addForm').classList.toggle('show')">
    Add New Animal
  </button>

  <div id="addForm" class="add-form">
    <h3>Add New Animal</h3>
    <form method="POST">
      <input type="text" name="name" placeholder="Name of Animal" required>
      <input type="text" name="type" placeholder="Type (Dog, Cat, etc.)" required>
      <input type="number" name="age" placeholder="Age (in years)" min="0">
      <select name="gender">
        <option value="">Gender</option>
        <option>Male</option>
        <option>Female</option>
      </select>
      <select name="status">
        <option>In Shelter</option>
        <option>Adopted</option>
        <option>Rescued</option>
        <option>Deceased</option>
      </select>
      <textarea name="description" placeholder="Description / Notes"></textarea>
      <button type="submit" name="add_animal">Add Animal</button>
    </form>
  </div>

</div>

<?php $conn->close(); ?>
</body>
</html>

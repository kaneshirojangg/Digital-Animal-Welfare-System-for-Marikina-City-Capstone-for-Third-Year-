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

		<style>
body {
  font-family: 'Inter', sans-serif;
  background: #f8fafc;
  margin: 0;
  margin-left: 280px;
}

.topbar {
  background: #2c7d4e;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h1 { margin: 0; }

.exit-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
}

.content {
  max-width: 1100px;
  margin: 40px auto;
  padding: 20px;
}

h1 {
  color: #2c7d4e;
  text-align: center;
  margin-bottom: 10px;
}

/* ✅ CENTERED SUBTITLE */
.subtitle {
  text-align: center;
  max-width: 700px;
  margin: 0 auto 30px auto;
  color: #4b5563;
  line-height: 1.6;
}

.add-btn {
  display: block;
  margin: 0 auto 30px;
  padding: 16px 40px;
  font-size: 1.1rem;
  background: #2c7d4e;
  color: white;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.add-form {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  margin-bottom: 40px;
  display: none;
}

.add-form.show { display: block; }

input, select, textarea {
  width: 100%;
  padding: 12px;
  margin-bottom: 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
}

button:hover { background: #1e5c38; }

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
}

th {
  background: #2c7d4e;
  color: white;
  padding: 14px;
}

td {
  padding: 14px;
  border-bottom: 1px solid #e5e7eb;
}

tr:hover { background: #f1f5f9; }
</style>
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

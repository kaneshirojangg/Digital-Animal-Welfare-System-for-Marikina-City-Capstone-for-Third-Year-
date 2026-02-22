<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_adoption'])) {
    $animal_name     = trim($_POST['animal_name'] ?? '');
    $animal_type     = trim($_POST['animal_type'] ?? '');
    $applicant_name  = trim($_POST['applicant_name'] ?? '');
    $applicant_contact = trim($_POST['applicant_contact'] ?? '');
    $notes           = trim($_POST['notes'] ?? '');

    if (empty($animal_name) || empty($animal_type) || empty($applicant_name)) {
        $error = "Kailangan punan ang Animal Name, Type, at Applicant Name.";
    } else {
        $stmt = $conn->prepare("INSERT INTO adoptions (animal_name, animal_type, applicant_name, applicant_contact, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $animal_name, $animal_type, $applicant_name, $applicant_contact, $notes);

        if ($stmt->execute()) {
            $success = "Bagong adoption request na naidagdag!";
        } else {
            $error = "Error sa pag-save: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT id, animal_name, animal_type, applicant_name, status, request_date FROM adoptions ORDER BY request_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adoptions - Marikina A&W</title>
  <style>
    body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
    .topbar { background: #2c7d4e; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .logo h1 { margin: 0; font-size: 1.6rem; }
    .exit-btn { background: #dc2626; color: white; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: bold; }
    .content { max-width: 1100px; margin: 40px auto; padding: 20px; }
    h1 { color: #2c7d4e; text-align: center; margin-bottom: 30px; }
    .add-form { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 40px; display: none; }
    .add-form.show { display: block; }
    input, textarea { width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; }
    button { background: #2c7d4e; color: white; border: none; padding: 14px 28px; border-radius: 8px; cursor: pointer; font-weight: 600; }
    button:hover { background: #1e5c38; }
    .message { padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    .success { background: #d1fae5; color: #065f46; }
    .error { background: #fee2e2; color: #991b1b; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-radius: 12px; overflow: hidden; }
    th, td { padding: 14px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    th { background: #2c7d4e; color: white; }
    tr:hover { background: #f1f5f9; }
    .add-btn { display: block; margin: 0 auto 30px; padding: 16px 40px; font-size: 1.1rem; }
  </style>
</head>
<body>

<?php include 'nav-menu.php'; ?>

  <!-- Main Content -->
  <div class="content">
    <h1>Adoptions Management</h1>
    <p>Dito mo mapapamahalaan ang mga adoption requests, approved adoptions, at animal profiles para sa adoption.</p>

    <!-- Success/Error Message -->
    <?php if (!empty($success)): ?>
      <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Add New Form (naka-hide muna) -->
    <button class="add-btn" onclick="document.getElementById('addForm').classList.toggle('show')">
      Add New Adoption Request
    </button>

    <div id="addForm" class="add-form">
      <form method="POST">
        <input type="text" name="animal_name" placeholder="Pangalan ng Hayop" required>
        <input type="text" name="animal_type" placeholder="Uri ng Hayop (Dog, Cat, etc.)" required>
        <input type="text" name="applicant_name" placeholder="Pangalan ng Applicant" required>
        <input type="text" name="applicant_contact" placeholder="Contact Number / Email">
        <textarea name="notes" placeholder="Additional Notes" rows="4"></textarea>
        <button type="submit" name="add_adoption">Submit Request</button>
      </form>
    </div>

    <!-- List ng Adoptions mula DB -->
    <?php
    if ($result && $result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Animal Name</th>
            <th>Type</th>
            <th>Applicant</th>
            <th>Status</th>
            <th>Date Requested</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['animal_name']); ?></td>
              <td><?php echo htmlspecialchars($row['animal_type']); ?></td>
              <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
              <td><?php echo $row['status']; ?></td>
              <td><?php echo date('M d, Y h:i A', strtotime($row['request_date'])); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="text-align:center; margin-top:40px;">Wala pang adoption requests. Magdagdag ng bago.</p>
    <?php endif; ?>
  </div>

<?php $conn->close(); ?>
</body>
</html>
</html>
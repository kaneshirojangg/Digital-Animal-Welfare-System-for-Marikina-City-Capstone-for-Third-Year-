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

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Stats
$total    = $conn->query("SELECT COUNT(*) as c FROM adoptions WHERE user_id = $user_id OR applicant_name = '$full_name'")->fetch_assoc()['c'];
$pending  = $conn->query("SELECT COUNT(*) as c FROM adoptions WHERE (user_id = $user_id OR applicant_name = '$full_name') AND status = 'Pending'")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) as c FROM adoptions WHERE (user_id = $user_id OR applicant_name = '$full_name') AND status = 'Approved'")->fetch_assoc()['c'];
$completed= $conn->query("SELECT COUNT(*) as c FROM adoptions WHERE (user_id = $user_id OR applicant_name = '$full_name') AND status = 'Completed'")->fetch_assoc()['c'];

$result = $conn->query("SELECT id, animal_name, animal_type, applicant_name, status, request_date FROM adoptions WHERE user_id = $user_id OR applicant_name = '$full_name' ORDER BY request_date DESC");

$conn->close();
$activePage = 'adoptions';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Adoption Requests – Marikina A&amp;W</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/nav.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'nav-menu.php'; ?>

<div class="main-content">

  <div class="page-header">
    <div>
      <h1>My Adoption Requests</h1>
      <p>Track the status of all your animal adoption applications</p>
    </div>
    <a href="adopt-animal.php" class="btn btn-primary">Browse Animals</a>
  </div>

  <!-- Stats -->
  <div class="stats-grid" style="grid-template-columns:repeat(4,1fr); margin-bottom:24px;">
    <div class="stat-card primary">
      <div class="stat-card-header"><h3>Total</h3></div>
      <div class="big-number"><?php echo $total; ?></div>
      <div class="stat-card-footer">All requests</div>
    </div>
    <div class="stat-card info">
      <div class="stat-card-header"><h3>Pending</h3></div>
      <div class="big-number"><?php echo $pending; ?></div>
      <div class="stat-card-footer">Awaiting review</div>
    </div>
    <div class="stat-card warning">
      <div class="stat-card-header"><h3>Approved</h3></div>
      <div class="big-number"><?php echo $approved; ?></div>
      <div class="stat-card-footer">Ready to proceed</div>
    </div>
    <div class="stat-card success">
      <div class="stat-card-header"><h3>Completed</h3></div>
      <div class="big-number"><?php echo $completed; ?></div>
      <div class="stat-card-footer">Successfully adopted</div>
    </div>
  </div>

  <!-- Table -->
  <?php if ($result && $result->num_rows > 0): ?>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Animal</th>
            <th>Type</th>
            <th>Applicant</th>
            <th>Status</th>
            <th>Date Requested</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()):
            $st  = $row['status'];
            $stc = 'status-badge status-' . strtolower($st);
          ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><strong><?php echo htmlspecialchars($row['animal_name']); ?></strong></td>
              <td><?php echo htmlspecialchars($row['animal_type']); ?></td>
              <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
              <td><span class="<?php echo $stc; ?>"><?php echo $st; ?></span></td>
              <td><?php echo date('M d, Y g:i A', strtotime($row['request_date'])); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">🐾</div>
      <h3>No Adoption Requests Yet</h3>
      <p>You haven't submitted any adoption requests yet.</p>
      <a href="adopt-animal.php" class="empty-state-btn">Browse Available Animals</a>
    </div>
  <?php endif; ?>

</div>

</body>
</html>
<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

$activePage = 'vaccinations';

$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$full_name = $_SESSION['full_name'];

$vaccinations = [];
$result = $conn->query("
    SELECT v.id, v.animal_name, v.vaccine_type, v.schedule_date, v.vet_staff, v.status 
    FROM vaccinations v
    INNER JOIN adoptions a ON v.animal_name = a.animal_name
    WHERE a.applicant_name = '$full_name' AND a.status = 'Completed'
    ORDER BY v.schedule_date DESC
");

while ($row = $result->fetch_assoc()) {
    $vaccinations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vaccination Records - Marikina A&W</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/nav.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>

<?php include 'nav-menu.php'; ?>

<div class="main-content">

  <div class="page-header">
    <h1>Vaccination Records</h1>
    <p>View vaccination schedules for your adopted animals</p>
  </div>

  <div class="info-box">
    <strong>ℹ️ Staff-Created Schedules:</strong> Vaccination schedules are created by our staff and veterinarians. Contact us if you have questions about your animal's health.
  </div>

  <?php if (count($vaccinations) > 0): ?>
    <div class="vaccine-grid">
      <?php foreach ($vaccinations as $vax): ?>
        <div class="vaccine-card">
          <h3><?php echo htmlspecialchars($vax['animal_name']); ?></h3>
          
          <div class="vaccine-info">
            <label>Vaccine Type</label>
            <p><?php echo htmlspecialchars($vax['vaccine_type']); ?></p>
          </div>

          <div class="vaccine-info">
            <label>Scheduled Date</label>
            <p><?php echo date('M d, Y @ h:i A', strtotime($vax['schedule_date'])); ?></p>
          </div>

          <div class="vaccine-info">
            <label>Veterinarian / Staff</label>
            <p><?php echo htmlspecialchars($vax['vet_staff'] ?? 'TBA'); ?></p>
          </div>

          <strong>Status:</strong>
          <div>
            <?php
              $statusClass = 'status-pending';
              if ($vax['status'] == 'Done') $statusClass = 'status-done';
              elseif ($vax['status'] == 'Scheduled') $statusClass = 'status-scheduled';
            ?>
            <span class="status-badge <?php echo $statusClass; ?>">
              <?php echo htmlspecialchars($vax['status']); ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <p>No Vaccination Records Yet</p>
      <small>Vaccination schedules will appear here once you adopt an animal and the staff creates a schedule for their wellness.</small>
    </div>
  <?php endif; ?>

</div>

</body>
</html>

<?php $conn->close(); ?>
<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
	header("Location: login.php");
	exit();
}

$activePage = 'schedule';

$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
	$title       = trim($_POST['title'] ?? '');
	$description = trim($_POST['description'] ?? '');
	$schedule_date = trim($_POST['schedule_date'] ?? '');
	$appointment_type = trim($_POST['appointment_type'] ?? 'General');

	if (empty($title) || empty($schedule_date)) {
		$error = "Please fill in the appointment title and date.";
	} else {

		$stmt = $conn->prepare("INSERT INTO vaccinations (animal_name, vaccine_type, schedule_date, vet_staff, status) VALUES (?, ?, ?, ?, ?)");
		$status = 'Scheduled';
		$stmt->bind_param("sssss", $title, $appointment_type, $schedule_date, $description, $status);

		if ($stmt->execute()) {
			$success = "Appointment added successfully!";
		} else {
			$error = "Error saving appointment: " . $stmt->error;
		}
		$stmt->close();
	}
}

$events = [];
$result = $conn->query("
	SELECT id, animal_name as title, vaccine_type as type, schedule_date, vet_staff, status 
	FROM vaccinations 
	WHERE animal_name IN (
		SELECT DISTINCT animal_name FROM adoptions WHERE applicant_name = '$full_name'
	)
	ORDER BY schedule_date
");

while ($row = $result->fetch_assoc()) {
	$color = '#8ECFC9'; 

	if ($row['status'] == 'Done') $color = 'var(--success)';
	if ($row['status'] == 'Cancelled') $color = '#dc2626';
	if (strtotime($row['schedule_date']) < time() && $row['status'] != 'Done' && $row['status'] != 'Cancelled') $color = '#f59e0b'; 

	$events[] = [
		'title' => $row['title'],
		'start' => $row['schedule_date'],
		'color' => $color,
		'extendedProps' => [
			'type' => $row['type'],
			'notes' => $row['vet_staff'],
			'status' => $row['status'],
			'id' => $row['id']
		]
	];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedule - Marikina A&W</title>

  <!-- FullCalendar CSS & JS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
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
    <h1>My Schedule</h1>
    <p>Manage your appointments and schedules</p>
  </div>

  <?php if (!empty($success)): ?>
    <div class="message success"><?php echo $success; ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="message error"><?php echo $error; ?></div>
  <?php endif; ?>

  <div class="action-bar">
    <button class="btn" onclick="openAddModal()">+ Add Appointment</button>
  </div>

  <div class="calendar-container">
    <div id="calendar"></div>
  </div>

</div>

<!-- Add Appointment Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h2>Add Appointment</h2>
    <form method="POST">

      <div class="form-group">
        <label>Appointment Title *</label>
        <input type="text" name="title" placeholder="e.g., Vet Checkup, Grooming" required>
      </div>

      <div class="form-group">
        <label>Type</label>
        <select name="appointment_type">
          <option value="General">General</option>
          <option value="Vaccination">Vaccination</option>
          <option value="Checkup">Medical Checkup</option>
          <option value="Grooming">Grooming</option>
          <option value="Training">Training</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Date & Time *</label>
        <input type="datetime-local" name="schedule_date" required>
      </div>

      <div class="form-group">
        <label>Notes / Details</label>
        <textarea name="description" placeholder="Additional information about the appointment..."></textarea>
      </div>

      <div class="modal-buttons">
        <button type="submit" name="add_schedule" class="btn-submit">Save Appointment</button>
        <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
      </div>

    </form>
  </div>
</div>

<script>
  function openAddModal() {
    document.getElementById('addModal').style.display = 'flex';
  }

  function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
  }

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: <?php echo json_encode($events); ?>,
      eventClick: function(info) {
        var details = 'Appointment: ' + info.event.title + 
                     '\nType: ' + info.event.extendedProps.type +
                     '\nStatus: ' + info.event.extendedProps.status;
        if (info.event.extendedProps.notes) {
          details += '\nNotes: ' + info.event.extendedProps.notes;
        }
        alert(details);
      }
    });
    calendar.render();
  });
</script>

</body>
</html>
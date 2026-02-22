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
	$color = '#2c7d4e'; 

	if ($row['status'] == 'Done') $color = '#065f46';
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

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --text: #2d3748;
      --text-light: #4b5563;
      --bg: #f8fafc;
      --border: #e2e8f0;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin-left: 280px;
      min-height: 100vh;
    }

    .main-content {
      max-width: 1300px;
      margin: 0 auto;
      padding: 40px 24px;
    }

    .page-header {
      margin-bottom: 40px;
    }

    .page-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.4rem;
      color: var(--primary-dark);
      margin-bottom: 8px;
    }

    .page-header p {
      color: var(--text-light);
      font-size: 1.05rem;
    }

    .action-bar {
      margin: 24px 0;
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background: var(--primary-dark);
      box-shadow: 0 4px 12px rgba(44, 125, 78, 0.2);
    }

    .message {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .message.success {
      background: #d1fae5;
      color: #065f46;
      border-left: 4px solid #10b981;
    }

    .message.error {
      background: #fee2e2;
      color: #991b1b;
      border-left: 4px solid #dc2626;
    }

    .calendar-container {
      background: white;
      padding: 20px;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 40px;
    }

    #calendar {
      font-family: 'Inter', sans-serif;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }

    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 12px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-content h2 {
      color: var(--primary-dark);
      margin-bottom: 20px;
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      margin-bottom: 4px;
      font-weight: 600;
      color: var(--text);
      font-size: 0.9rem;
    }

    input, select, textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-family: 'Inter', sans-serif;
      font-size: 0.95rem;
    }

    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(44, 125, 78, 0.1);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .modal-buttons {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .modal-buttons button {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .modal-buttons .btn-submit {
      background: var(--primary);
      color: white;
    }

    .modal-buttons .btn-submit:hover {
      background: var(--primary-dark);
    }

    .modal-buttons .btn-cancel {
      background: #e5e7eb;
      color: var(--text);
    }

    .modal-buttons .btn-cancel:hover {
      background: #d1d5db;
    }

    @media (max-width: 768px) {
      body { margin-left: 0; }
      .main-content { padding: 20px; }
      .page-header h1 { font-size: 1.8rem; }
      .action-bar { justify-content: center; }
    }
  </style>
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
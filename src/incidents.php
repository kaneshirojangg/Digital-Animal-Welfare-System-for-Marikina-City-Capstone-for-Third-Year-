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

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_incident'])) {

	$incident_date = trim($_POST['incident_date'] ?? '');
	$incident_time = trim($_POST['incident_time'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$barangay = trim($_POST['barangay'] ?? '');
	$animal_type = trim($_POST['animal_type'] ?? '');
	$animal_color = trim($_POST['animal_color'] ?? '');
	$animal_size = trim($_POST['animal_size'] ?? '');
	$animal_features = trim($_POST['animal_features'] ?? '');
	$victim_name = trim($_POST['victim_name'] ?? '');
	$victim_age = !empty($_POST['victim_age']) ? intval($_POST['victim_age']) : NULL;
	$victim_contact = trim($_POST['victim_contact'] ?? '');
	$injury_description = trim($_POST['injury_description'] ?? '');
	$severity = trim($_POST['severity'] ?? 'Medium');
	$treatment = trim($_POST['treatment'] ?? '');
	$remarks = trim($_POST['remarks'] ?? '');

	if (empty($incident_date) || empty($incident_time) || empty($location) || empty($barangay) || 
	    empty($animal_type) || empty($victim_name) || empty($injury_description)) {
		$error = "⚠ Please fill out all required fields (marked with *)";
	} else if (strtotime($incident_date) > time()) {
		$error = "⚠ Incident date cannot be in the future";
	} else if (!in_array($severity, ['Low', 'Medium', 'High', 'Critical'])) {
		$error = "⚠ Invalid severity level";
	} else {

		$stmt = $conn->prepare("
			INSERT INTO incidents 
			(user_id, incident_date, incident_time, location, barangay, animal_type, animal_color, 
			 animal_size, animal_distinguishing_features, victim_name, victim_age, victim_contact, 
			 injury_description, severity_level, treatment_received, remarks) 
			VALUES 
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		");

		if (!$stmt) {
			$error = "❌ Database error: " . $conn->error;
		} else {
			$stmt->bind_param(
				"issssssssssissss",
				$user_id, $incident_date, $incident_time, $location, $barangay, $animal_type, 
				$animal_color, $animal_size, $animal_features, $victim_name, $victim_age, $victim_contact, 
				$injury_description, $severity, $treatment, $remarks
			);

			if ($stmt->execute()) {
				$success = "✅ Incident report submitted successfully! Reference ID: #" . $conn->insert_id;

				$incident_date = $incident_time = $location = $barangay = $animal_type = $animal_color = '';
				$animal_size = $animal_features = $victim_name = $victim_age = $victim_contact = '';
				$injury_description = $severity = $treatment = $remarks = '';
			} else {
				$error = "❌ Error submitting report: " . $stmt->error;
			}
			$stmt->close();
		}
	}
}

$incidents_query = $conn->query("
	SELECT id, incident_date, incident_time, location, barangay, animal_type, 
	       victim_name, injury_description, severity_level, status, created_at 
	FROM incidents 
	WHERE user_id = '$user_id' 
	ORDER BY created_at DESC 
	LIMIT 10
");

$activePage = 'incidents';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Report Incident – Marikina Animal & Welfare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --accent: #e67e22;
      --danger: #dc2626;
      --warning: #f59e0b;
      --success: #10b981;
      --text: #2d3748;
      --text-light: #4b5563;
      --bg: #f8fafc;
      --card: #ffffff;
      --border: #e2e8f0;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
      min-height: 100vh;
      margin-left: 280px;
    }

    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 24px;
    }

    .page-title {
      font-family: 'Playfair Display', serif;
      color: var(--primary-dark);
      font-size: 2.4rem;
      margin-bottom: 12px;
    }

    .page-subtitle {
      color: var(--text-light);
      font-size: 1rem;
      margin-bottom: 30px;
    }

    .alert {
      padding: 16px 20px;
      border-radius: 12px;
      margin-bottom: 24px;
      font-weight: 500;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
      border-left: 4px solid var(--success);
    }

    .alert-error {
      background: #fee2e2;
      color: #991b1b;
      border-left: 4px solid var(--danger);
    }

    .form-container {
      background: var(--card);
      border-radius: 12px;
      padding: 32px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 40px;
    }

    .form-section {
      margin-bottom: 32px;
    }

    .form-section-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary-dark);
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--border);
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }

    .form-grid.full {
      grid-template-columns: 1fr;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text);
      font-size: 0.95rem;
    }

    .required {
      color: var(--danger);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 12px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-family: 'Inter', sans-serif;
      font-size: 0.95rem;
      transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(44, 125, 78, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }

    .submit-btn {
      background: var(--primary);
      color: white;
      padding: 14px 32px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
    }

    .submit-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(44, 125, 78, 0.2);
    }

    .incidents-section {
      margin-top: 50px;
    }

    .incidents-title {
      font-family: 'Playfair Display', serif;
      color: var(--primary-dark);
      font-size: 1.8rem;
      margin-bottom: 24px;
    }

    .incidents-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .incident-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .incident-card:hover {
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transform: translateY(-4px);
    }

    .incident-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 12px;
    }

    .incident-date {
      font-size: 0.85rem;
      color: var(--text-light);
      font-weight: 500;
    }

    .severity-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .severity-low {
      background: #d1fae5;
      color: #065f46;
    }

    .severity-medium {
      background: #fef3c7;
      color: #92400e;
    }

    .severity-high {
      background: #fed7aa;
      color: #92400e;
    }

    .severity-critical {
      background: #fee2e2;
      color: #991b1b;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 12px;
    }

    .status-new {
      background: #dbeafe;
      color: #1e40af;
    }

    .status-review {
      background: #fef3c7;
      color: #92400e;
    }

    .status-resolved {
      background: #d1fae5;
      color: #065f46;
    }

    .incident-info {
      margin-bottom: 12px;
    }

    .incident-label {
      font-size: 0.8rem;
      color: var(--text-light);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }

    .incident-value {
      font-size: 0.95rem;
      color: var(--text);
      margin-top: 4px;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: var(--text-light);
    }

    .empty-state-icon {
      font-size: 3rem;
      margin-bottom: 16px;
    }

    @media (max-width: 768px) {
      body { margin-left: 0; }
      .main-content { padding: 20px; }
      .page-title { font-size: 1.8rem; }
      .form-container { padding: 20px; }
      .form-grid { grid-template-columns: 1fr; }
      .incidents-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php include 'nav-menu.php'; ?>

<div class="main-content">
  
  <h1 class="page-title">Report Incident / Bite Case</h1>
  <p class="page-subtitle">Help us keep the community safe by reporting animal incidents</p>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
  <?php endif; ?>

  <!-- Incident Report Form -->
  <div class="form-container">
    <form method="POST" action="">

      <!-- Incident Details Section -->
      <div class="form-section">
        <div class="form-section-title">Incident Details</div>
        <div class="form-grid">
          <div class="form-group">
            <label>Date of Incident <span class="required">*</span></label>
            <input type="date" name="incident_date" required value="<?php echo htmlspecialchars($incident_date ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Time of Incident <span class="required">*</span></label>
            <input type="time" name="incident_time" required value="<?php echo htmlspecialchars($incident_time ?? ''); ?>">
          </div>
        </div>
      </div>

      <!-- Location Section -->
      <div class="form-section">
        <div class="form-section-title">📍 Location</div>
        <div class="form-grid">
          <div class="form-group">
            <label>Barangay <span class="required">*</span></label>
            <select name="barangay" required>
              <option value="">-- Select Barangay --</option>
              <option value="Barangay Nangka" <?php echo $barangay === 'Barangay Nangka' ? 'selected' : ''; ?>>Barangay Nangka</option>
              <option value="Barangay Fortune" <?php echo $barangay === 'Barangay Fortune' ? 'selected' : ''; ?>>Barangay Fortune</option>
              <option value="Barangay Concepcion" <?php echo $barangay === 'Barangay Concepcion' ? 'selected' : ''; ?>>Barangay Concepcion</option>
              <option value="Barangay Marikina Heights" <?php echo $barangay === 'Barangay Marikina Heights' ? 'selected' : ''; ?>>Barangay Marikina Heights</option>
              <option value="Barangay San Roque" <?php echo $barangay === 'Barangay San Roque' ? 'selected' : ''; ?>>Barangay San Roque</option>
              <option value="Barangay Calentita" <?php echo $barangay === 'Barangay Calentita' ? 'selected' : ''; ?>>Barangay Calentita</option>
              <option value="Barangay Taguig" <?php echo $barangay === 'Barangay Taguig' ? 'selected' : ''; ?>>Barangay Taguig</option>
              <option value="Barangay Yeray" <?php echo $barangay === 'Barangay Yeray' ? 'selected' : ''; ?>>Barangay Yeray</option>
            </select>
          </div>
          <div class="form-group full">
            <label>Specific Location / Address <span class="required">*</span></label>
            <input type="text" name="location" required placeholder="e.g., Near St. Cecilia School, M.L. Quezon Avenue" value="<?php echo htmlspecialchars($location ?? ''); ?>">
          </div>
        </div>
      </div>

      <!-- Animal Information Section -->
      <div class="form-section">
        <div class="form-section-title">Animal Information</div>
        <div class="form-grid">
          <div class="form-group">
            <label>Animal Type <span class="required">*</span></label>
            <select name="animal_type" required>
              <option value="">-- Select Type --</option>
              <option value="Dog" <?php echo $animal_type === 'Dog' ? 'selected' : ''; ?>>Dog</option>
              <option value="Cat" <?php echo $animal_type === 'Cat' ? 'selected' : ''; ?>>Cat</option>
              <option value="Other" <?php echo $animal_type === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>
          <div class="form-group">
            <label>Animal Color</label>
            <input type="text" name="animal_color" placeholder="e.g., Brown and white" value="<?php echo htmlspecialchars($animal_color ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Animal Size</label>
            <select name="animal_size">
              <option value="">-- Select Size --</option>
              <option value="Small" <?php echo $animal_size === 'Small' ? 'selected' : ''; ?>>Small</option>
              <option value="Medium" <?php echo $animal_size === 'Medium' ? 'selected' : ''; ?>>Medium</option>
              <option value="Large" <?php echo $animal_size === 'Large' ? 'selected' : ''; ?>>Large</option>
            </select>
          </div>
        </div>
        <div class="form-grid full">
          <div class="form-group">
            <label>Distinguishing Features (scars, collar, tags, etc.)</label>
            <textarea name="animal_features" placeholder="Describe any identifying marks or features..."></textarea>
          </div>
        </div>
      </div>

      <!-- Victim Information Section -->
      <div class="form-section">
        <div class="form-section-title">👤 Victim Information</div>
        <div class="form-grid">
          <div class="form-group">
            <label>Victim Name <span class="required">*</span></label>
            <input type="text" name="victim_name" required placeholder="Full name of person bitten/attacked" value="<?php echo htmlspecialchars($victim_name ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Age</label>
            <input type="number" name="victim_age" min="0" max="120" placeholder="Age" value="<?php echo htmlspecialchars($victim_age ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="victim_contact" placeholder="09xx-xxx-xxxx" value="<?php echo htmlspecialchars($victim_contact ?? ''); ?>">
          </div>
        </div>
      </div>

      <!-- Injury Information Section -->
      <div class="form-section">
        <div class="form-section-title">⚠️ Injury Information</div>
        <div class="form-grid full">
          <div class="form-group">
            <label>Description of Injury <span class="required">*</span></label>
            <textarea name="injury_description" required placeholder="Describe the injury in detail (location on body, depth, severity, etc.)..."></textarea>
          </div>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label>Severity Level <span class="required">*</span></label>
            <select name="severity" required>
              <option value="Low" <?php echo $severity === 'Low' ? 'selected' : ''; ?>>Low (scratch, minor bite)</option>
              <option value="Medium" selected>Medium (moderate wound)</option>
              <option value="High" <?php echo $severity === 'High' ? 'selected' : ''; ?>>High (deep wound, bleeding)</option>
              <option value="Critical" <?php echo $severity === 'Critical' ? 'selected' : ''; ?>>Critical (requires urgent medical attention)</option>
            </select>
          </div>
          <div class="form-group full">
            <label>Treatment Received</label>
            <input type="text" name="treatment" placeholder="e.g., First aid at home, Hospital visit, Anti-rabies treatment" value="<?php echo htmlspecialchars($treatment ?? ''); ?>">
          </div>
        </div>
      </div>

      <!-- Additional Remarks -->
      <div class="form-section">
        <div class="form-section-title">📝 Additional Remarks</div>
        <div class="form-grid full">
          <div class="form-group">
            <label>Any other relevant information</label>
            <textarea name="remarks" placeholder="Any additional details about the incident..."></textarea>
          </div>
        </div>
      </div>

      <button type="submit" name="report_incident" class="submit-btn">Submit Incident Report</button>

    </form>
  </div>

  <!-- My Reported Incidents Section -->
  <div class="incidents-section">
    <h2 class="incidents-title">My Reported Incidents</h2>
    
    <?php if ($incidents_query && $incidents_query->num_rows > 0): ?>
      <div class="incidents-grid">
        <?php while ($incident = $incidents_query->fetch_assoc()): 
          $status_class = 'status-' . strtolower(str_replace(' ', '-', $incident['status']));
          $severity_class = 'severity-' . strtolower($incident['severity_level']);
        ?>
          <div class="incident-card">
            <div class="incident-header">
              <div class="incident-date">
                <?php echo date('M d, Y', strtotime($incident['incident_date'])); ?> 
                at <?php echo date('h:i A', strtotime($incident['incident_time'])); ?>
              </div>
              <span class="severity-badge <?php echo $severity_class; ?>">
                <?php echo htmlspecialchars($incident['severity_level']); ?>
              </span>
            </div>

            <span class="status-badge <?php echo $status_class; ?>">
              <?php echo htmlspecialchars($incident['status']); ?>
            </span>

            <div class="incident-info">
              <div class="incident-label">📍 Location</div>
              <div class="incident-value"><?php echo htmlspecialchars($incident['barangay'] . ' - ' . $incident['location']); ?></div>
            </div>

            <div class="incident-info">
              <div class="incident-label">Animal Type</div>
              <div class="incident-value"><?php echo htmlspecialchars($incident['animal_type']); ?></div>
            </div>

            <div class="incident-info">
              <div class="incident-label">👤 Victim</div>
              <div class="incident-value"><?php echo htmlspecialchars($incident['victim_name']); ?></div>
            </div>

            <div class="incident-info">
              <div class="incident-label">⚠️ Injury</div>
              <div class="incident-value"><?php echo htmlspecialchars(substr($incident['injury_description'], 0, 60)) . (strlen($incident['injury_description']) > 60 ? '...' : ''); ?></div>
            </div>

            <div class="incident-info">
              <div class="incident-label">Reported On</div>
              <div class="incident-value"><?php echo date('M d, Y', strtotime($incident['created_at'])); ?></div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <p>No incident reports yet. Submit your first report above.</p>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php $conn->close(); ?>

</body>
</html>
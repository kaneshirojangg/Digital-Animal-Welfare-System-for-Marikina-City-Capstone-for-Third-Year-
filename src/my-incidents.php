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

// Handle detail view modal
$view_incident = null;
if (!empty($_GET['view'])) {
  $view_id = (int) $_GET['view'];
  $view_result = $conn->query("SELECT * FROM incidents WHERE id = $view_id AND user_id = '$user_id' LIMIT 1");
  if ($view_result && $view_result->num_rows > 0) {
    $view_incident = $view_result->fetch_assoc();
  }
}

// Fetch all user incidents
$incidents_query = $conn->query("
  SELECT id, incident_date, incident_time, location, barangay, animal_type,
         victim_name, injury_description, severity_level, status, created_at
  FROM incidents
  WHERE user_id = '$user_id'
  ORDER BY created_at DESC
");

// Calculate stats
$stats = ['Total' => 0, 'New' => 0, 'Under Review' => 0, 'Resolved' => 0];
if ($incidents_query && $incidents_query->num_rows > 0) {
  while ($row = $incidents_query->fetch_assoc()) {
    $stats['Total']++;
    $s = $row['status'] ?? 'New';
    if (isset($stats[$s])) $stats[$s]++;
  }
  $incidents_query->data_seek(0);
}

$conn->close();
$activePage = 'my-incidents';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Reports – Marikina Animal & Welfare</title>
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
      <h1>My Reports</h1>
      <p>Track all your submitted incident reports</p>
    </div>
    <a href="incidents.php" class="btn btn-primary">+ New Report</a>
  </div>

  <!-- Stats -->
  <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
    <div class="stat-card primary">
      <div class="stat-card-header"><h3>Total Reports</h3></div>
      <div class="big-number"><?php echo $stats['Total']; ?></div>
      <div class="stat-card-footer">All submitted reports</div>
    </div>
    <div class="stat-card info">
      <div class="stat-card-header"><h3>Pending</h3></div>
      <div class="big-number"><?php echo $stats['New']; ?></div>
      <div class="stat-card-footer">Awaiting review</div>
    </div>
    <div class="stat-card warning">
      <div class="stat-card-header"><h3>In Progress</h3></div>
      <div class="big-number"><?php echo $stats['Under Review']; ?></div>
      <div class="stat-card-footer">Under review</div>
    </div>
    <div class="stat-card success">
      <div class="stat-card-header"><h3>Resolved</h3></div>
      <div class="big-number"><?php echo $stats['Resolved']; ?></div>
      <div class="stat-card-footer">Cases resolved</div>
    </div>
  </div>

  <!-- Table -->
  <?php if ($incidents_query && $incidents_query->num_rows > 0): ?>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Location</th>
            <th>Animal Type</th>
            <th>Victim Name</th>
            <th>Severity</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($incident = $incidents_query->fetch_assoc()):
            $status   = $incident['status'] ?? 'New';
            $severity = $incident['severity_level'] ?? 'Medium';
            $sev_colors = [
              'Low'      => 'background:rgba(16,185,129,.15);color:#059669;',
              'Medium'   => 'background:rgba(245,158,11,.15);color:#d97706;',
              'High'     => 'background:rgba(239,68,68,.15);color:#dc2626;',
              'Critical' => 'background:rgba(127,29,29,.18);color:#7f1d1d;',
            ];
            $sev_style = $sev_colors[$severity] ?? $sev_colors['Medium'];
          ?>
            <tr>
              <td><?php echo date('M d, Y', strtotime($incident['incident_date'])); ?></td>
              <td>
                <strong><?php echo htmlspecialchars($incident['location']); ?></strong><br>
                <small style="color:var(--text-light);"><?php echo htmlspecialchars($incident['barangay']); ?></small>
              </td>
              <td><?php echo htmlspecialchars($incident['animal_type']); ?></td>
              <td><?php echo htmlspecialchars($incident['victim_name']); ?></td>
              <td>
                <span style="<?php echo $sev_style; ?> padding:4px 10px; border-radius:20px; font-size:0.8rem; font-weight:600; display:inline-block;">
                  <?php echo $severity; ?>
                </span>
              </td>
              <td>
                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                  <?php echo $status; ?>
                </span>
              </td>
              <td><?php echo date('M d, Y', strtotime($incident['created_at'])); ?></td>
              <td>
                <a href="my-incidents.php?view=<?php echo $incident['id']; ?>" class="btn btn-secondary" style="padding:5px 14px; font-size:0.82rem;">View</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">📋</div>
      <h3>No Reports Yet</h3>
      <p>You haven't submitted any incident reports yet.</p>
      <a href="incidents.php" class="empty-state-btn">Submit Your First Report</a>
    </div>
  <?php endif; ?>

</div><!-- /.main-content -->

<!-- ===== Incident Detail Modal ===== -->
<?php if ($view_incident): ?>
<div id="incidentDetailModal" class="modal" style="display:flex; align-items:flex-start; padding-top:40px;">
  <div class="modal-content" style="max-width:680px; width:100%; padding:36px 40px; border-radius:16px; text-align:left; max-height:90vh; overflow-y:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <h2 style="margin:0; font-size:1.4rem; color:var(--text-dark);">
        Incident Report <span style="color:var(--primary);">#<?php echo $view_incident['id']; ?></span>
      </h2>
      <a href="my-incidents.php" style="font-size:1.4rem; color:var(--text-light); text-decoration:none; line-height:1;" title="Close">&times;</a>
    </div>

    <?php
      $vi = $view_incident;
      $sev = $vi['severity_level'] ?? 'Medium';
      $sev_map = [
        'Low'      => 'background:rgba(16,185,129,.15);color:#059669;',
        'Medium'   => 'background:rgba(245,158,11,.15);color:#d97706;',
        'High'     => 'background:rgba(239,68,68,.15);color:#dc2626;',
        'Critical' => 'background:rgba(127,29,29,.18);color:#7f1d1d;',
      ];
      $sev_s = $sev_map[$sev] ?? $sev_map['Medium'];
      $stat = $vi['status'] ?? 'New';
      $stat_cls = 'status-badge status-' . strtolower(str_replace(' ', '-', $stat));
    ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Incident Date</p>
        <p style="margin:0; font-weight:600;"><?php echo date('F d, Y', strtotime($vi['incident_date'])); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Incident Time</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['incident_time'] ?? '—'); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Location</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['location']); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Barangay</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['barangay']); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Animal Type</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['animal_type']); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Animal Color</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['animal_color'] ?? '—'); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Victim Name</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['victim_name']); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Victim Age</p>
        <p style="margin:0; font-weight:600;"><?php echo htmlspecialchars($vi['victim_age'] ?? '—'); ?></p>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Severity</p>
        <span style="<?php echo $sev_s; ?> padding:4px 12px; border-radius:20px; font-size:0.82rem; font-weight:600; display:inline-block;"><?php echo $sev; ?></span>
      </div>
      <div>
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Status</p>
        <span class="<?php echo $stat_cls; ?>"><?php echo $stat; ?></span>
      </div>
      <div style="grid-column:1/-1;">
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Injury Description</p>
        <p style="margin:0;"><?php echo nl2br(htmlspecialchars($vi['injury_description'] ?? '—')); ?></p>
      </div>
      <?php if (!empty($vi['treatment_received'])): ?>
      <div style="grid-column:1/-1;">
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Treatment Received</p>
        <p style="margin:0;"><?php echo nl2br(htmlspecialchars($vi['treatment_received'])); ?></p>
      </div>
      <?php endif; ?>
      <?php if (!empty($vi['remarks'])): ?>
      <div style="grid-column:1/-1;">
        <p style="margin:0 0 4px; font-size:0.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;">Remarks</p>
        <p style="margin:0;"><?php echo nl2br(htmlspecialchars($vi['remarks'])); ?></p>
      </div>
      <?php endif; ?>

    </div><!-- /grid -->

    <div style="margin-top:28px; text-align:right;">
      <a href="my-incidents.php" class="btn btn-primary">Close</a>
    </div>

  </div>
</div>
<?php endif; ?>

</body>
</html>

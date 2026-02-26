<?php

$activePage = $activePage ?? '';
?>
<aside class="sidebar">
	<div class="sidebar-header">
		<h2>Marikina A&W</h2>
		<p>Animal & Welfare</p>
	</div>

	<nav class="sidebar-nav">
		<a href="dashboard.php" class="sidebar-link <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">
			<span>Dashboard</span>
		</a>
		
		<a href="adopt-animal.php" class="sidebar-link <?php echo ($activePage === 'adopt') ? 'active' : ''; ?>">
			<span>Adopt Animal</span>
		</a>
		
		<a href="incidents.php" class="sidebar-link <?php echo ($activePage === 'incidents') ? 'active' : ''; ?>">
			<span>Report Incident</span>
		</a>
		
		<a href="my-incidents.php" class="sidebar-link <?php echo ($activePage === 'my-incidents') ? 'active' : ''; ?>">
			<span>My Reports</span>
		</a>
		
		<a href="vaccinations.php" class="sidebar-link <?php echo ($activePage === 'vaccinations') ? 'active' : ''; ?>">
			<span>Vaccinations</span>
		</a>
		
		<a href="reports.php" class="sidebar-link <?php echo ($activePage === 'reports') ? 'active' : ''; ?>">
			<span>Analytics</span>
		</a>
		
		<a href="schedule.php" class="sidebar-link <?php echo ($activePage === 'schedule') ? 'active' : ''; ?>">
			<span>Schedule</span>
		</a>
	</nav>

	<div class="sidebar-footer">
		<div class="user-info">
			<p class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></p>
			<p class="user-role">Staff</p>
		</div>
		<a href="logout.php" class="logout-btn">
			<span>Logout</span>
		</a>
	</div>
</aside>

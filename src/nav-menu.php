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
			<span>Incidents</span>
		</a>
		
		<a href="vaccinations.php" class="sidebar-link <?php echo ($activePage === 'vaccinations') ? 'active' : ''; ?>">
			<span>Vaccinations</span>
		</a>
		
		<a href="reports.php" class="sidebar-link <?php echo ($activePage === 'reports') ? 'active' : ''; ?>">
			<span>Reports</span>
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

<style>
.sidebar {
	width: 280px;
	background: linear-gradient(135deg, #2c7d4e 0%, #1e5c38 100%);
	color: white;
	padding: 24px 0;
	height: 100vh;
	position: fixed;
	left: 0;
	top: 0;
	overflow-y: auto;
	box-shadow: 2px 0 8px rgba(0,0,0,0.1);
	z-index: 1000;
	display: flex;
	flex-direction: column;
}

.sidebar-header {
	padding: 24px 20px;
	text-align: center;
	border-bottom: 1px solid rgba(255,255,255,0.15);
	margin-bottom: 20px;
}

.sidebar-header h2 {
	font-size: 1.4rem;
	margin: 0 0 4px 0;
	font-weight: 700;
}

.sidebar-header p {
	font-size: 0.85rem;
	opacity: 0.85;
	margin: 0;
}

.sidebar-nav {
	flex: 1;
	padding: 0 12px;
}

.sidebar-link {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 16px;
	color: rgba(255,255,255,0.85);
	text-decoration: none;
	border-radius: 8px;
	margin-bottom: 8px;
	transition: all 0.3s ease;
	font-size: 0.95rem;
}

.sidebar-link:hover {
	background: rgba(255,255,255,0.1);
	color: white;
	transform: translateX(4px);
}

.sidebar-link.active {
	background: rgba(255,255,255,0.25);
	color: white;
	font-weight: 600;
	border-left: 3px solid rgba(255,255,255,0.5);
	padding-left: 13px;
}

.sidebar-footer {
	padding: 20px;
	border-top: 1px solid rgba(255,255,255,0.15);
	background: rgba(0,0,0,0.1);
}

.user-info {
	margin-bottom: 16px;
	padding-bottom: 16px;
	border-bottom: 1px solid rgba(255,255,255,0.15);
}

.user-name {
	margin: 0;
	font-weight: 600;
	font-size: 0.95rem;
}

.user-role {
	margin: 4px 0 0 0;
	font-size: 0.85rem;
	opacity: 0.85;
}

.logout-btn {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 10px 14px;
	background: rgba(255,255,255,0.15);
	color: white;
	text-decoration: none;
	border-radius: 6px;
	transition: all 0.3s ease;
	font-size: 0.9rem;
	border: 1px solid rgba(255,255,255,0.25);
	cursor: pointer;
	width: 100%;
	text-align: left;
}

.logout-btn:hover {
	background: rgba(220, 38, 38, 0.8);
	border-color: rgba(220, 38, 38, 1);
}

/* Add margin to body content when sidebar is present */
body {
	margin-left: 280px;
}

@media (max-width: 768px) {
	.sidebar {
		width: 240px;
	}
	body {
		margin-left: 0;
	}
	.sidebar {
		transform: translateX(-100%);
		transition: transform 0.3s ease;
	}
	.sidebar.active {
		transform: translateX(0);
	}
}
</style>

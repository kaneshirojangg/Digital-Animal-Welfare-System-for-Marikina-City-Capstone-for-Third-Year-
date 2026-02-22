<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Marikina City - Community & Animal Welfare Management System</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --accent: #e67e22;
      --text: #2d3748;
      --light-bg: #f9fafb;
      --white: #ffffff;
      --border: #e2e8f0;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--light-bg);
      color: var(--text);
      line-height: 1.6;
    }

    nav {
      background: var(--white);
      border-bottom: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .nav-container {
      padding: 16px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .nav-brand {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      color: var(--primary-dark);
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
    }

    .nav-brand:hover {
      color: var(--primary);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .nav-links a {
      color: var(--text);
      text-decoration: none;
      font-weight: 500;
    }

    .nav-links a:hover,
    .nav-links a.active {
      color: var(--primary);
    }

    .btn-dashboard {
      background: var(--primary);
      color: white !important;
      padding: 10px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-dashboard:hover {
      background: var(--primary-dark);
      box-shadow: 0 4px 12px rgba(44, 125, 78, 0.2);
    }

    header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      text-align: center;
      padding: 80px 20px 60px;
    }

    header h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.4rem, 6.5vw, 4.2rem);
      margin-bottom: 16px;
    }

    header .tagline {
      font-size: clamp(1.1rem, 3vw, 1.35rem);
      max-width: 800px;
      margin: 0 auto;
      opacity: 0.95;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 24px;
    }

    section {
      background: var(--white);
      padding: 32px;
      margin-bottom: 40px;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }

    h2 {
      color: var(--primary);
      font-family: 'Playfair Display', serif;
      font-size: 2.1rem;
      margin-bottom: 1em;
      border-bottom: 3px solid var(--accent);
      display: inline-block;
      padding-bottom: 8px;
    }

    h3 {
      font-size: 1.35rem;
      color: var(--primary-dark);
      margin: 2rem 0 1rem;
    }

    ul {
      padding-left: 1.8em;
      margin: 1em 0;
    }

    li {
      margin-bottom: 0.6em;
      font-size: 1.05rem;
    }

    .highlight {
      background: rgba(44, 125, 78, 0.08);
      padding: 2px 8px;
      border-radius: 4px;
      color: var(--primary-dark);
    }

    @media (max-width: 768px) {
      header { padding: 60px 16px 40px; }
      .nav-container { flex-direction: column; gap: 12px; }
      .nav-links { text-align: center; margin-top: 8px; }
      section { padding: 24px 16px; }
    }
  </style>
</head>

<body>

<nav>
  <div class="nav-container">
    <a href="index.php" class="nav-brand">Marikina Animal & Welfare</a>

    <div class="nav-links">
      <a href="login.php" class="btn-dashboard">
        Log In
      </a>
    </div>
  </div>
</nav>

<header>
  <h1>Marikina City<br>Community & Animal Welfare<br>Management System</h1>
  <div class="tagline">
    A Proposed Digital Platform for Efficient Stray Animal Incident Management,<br>
    Anti-Rabies Response, Rescue, Adoption, and Welfare Program Monitoring
  </div>
</header>

<div class="container">
  <section>
    <h2>About Us</h2>
    <p>
      The <strong>Integrated Community and Animal Welfare Management and Analytics System</strong> is a web-based platform designed specifically for <strong>Marikina City LGU</strong> to address critical challenges in animal welfare and community services management.
    </p>
    <h3>Our Purpose</h3>
    <p>
      Marikina City faces challenges in managing stray animal incidents, animal bite cases, and community welfare services. Manual and fragmented record-keeping systems cause delays in response, inaccurate data, and difficulty identifying high-risk areas. Our system centralizes all welfare and animal-related data into one unified platform.
    </p>
    <h3>What We Do</h3>
    <ul>
      <li><strong>Incident Management</strong> – Track and monitor bite incidents and animal welfare concerns across barangays</li>
      <li><strong>Rescue Operations</strong> – Coordinate stray animal rescues with real-time updates and status tracking</li>
      <li><strong>Vaccination Monitoring</strong> – Manage vaccination schedules and anti-rabies programs efficiently</li>
      <li><strong>Adoption Services</strong> – Streamline the adoption process and track animal placements</li>
      <li><strong>Analytics & Reporting</strong> – Generate insights on high-risk areas, trends, and program performance</li>
    </ul>
    <h3>Why It Matters</h3>
    <p>
      By implementing this system, Marikina City improves <span class="highlight">rabies prevention efforts</span>, optimizes <span class="highlight">resource allocation</span>, enhances <span class="highlight">response time</span>, and increases <span class="highlight">transparency</span> in welfare services for both residents and animals. Role-based access control ensures data security while enabling staff, veterinarians, and administrators to collaborate effectively.
    </p>
  </section>
</div>

</body>
</html>

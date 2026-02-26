# Capstone Defense Guide
## Marikina City Animal & Welfare Management System

> Use this as your script reference during the panel defense. Each section maps directly to the rubric criteria. Bold lines are your cue points — memorize the opening and closing of each section, then fill the middle naturally.

---

## PRE-DEFENSE CHECKLIST

- [ ] System is running locally (`php -S localhost:8000 -t src`)
- [ ] Test login works — register a fresh account the morning of defense
- [ ] Browser is zoomed to ~90% so the full sidebar + content is visible
- [ ] Have the rubric open on your phone as a backup reference
- [ ] Assign roles: who presents which section (see role assignments below)
- [ ] Demo flow decided and practiced at least twice end-to-end

---

## PANEL RUBRIC BREAKDOWN & DEFENSE FLOW

---

### I. TITLE & AUTHOR INFORMATION — 5 pts

**What panels check:**
- Title is technical, solution-oriented, and IT-focused
- Title reflects the problem AND proposed system — not just the system name
- Authors, affiliation, program, adviser are complete

**Your title should sound like:**
> *"A Web-Based Animal Welfare Management System for Marikina City: Digitizing Incident Reporting, Adoption Processing, and Vaccination Tracking"*

**Key points to hit:**
- The phrase "Web-Based" signals an IT solution, not just a concept
- "Marikina City" anchors it to a real-world, localized problem
- Listing the three core modules (incidents, adoption, vaccination) signals the system is multi-functional and solution-specific

**Manuscript (Opening Statement — Presenter 1):**

> "Good morning, panel. Our research is titled: *'A Web-Based Animal Welfare Management System for Marikina City.'* The title reflects both the identified problem — the lack of a centralized digital system for managing animal welfare operations in Marikina — and our proposed IT solution, which is a fully functional web application that handles incident reporting, adoption request processing, and vaccination scheduling. We are [names], third-year BSIT students under the supervision of [adviser name]."

---

### II. ABSTRACT & KEYWORDS — 10 pts

**What panels check:**
- Abstract must state: real-world IT problem, research/system gap, proposed IT solution, design approach
- IEEE-compliant: 150–250 words, 1 paragraph, no citations
- Keywords (3–5) are technical and IT-relevant

**Key points to hit:**
- Open with the real-world problem (fragmented manual records in animal welfare offices)
- State the gap (no unified digital platform for Marikina City)
- Describe the solution (PHP/MySQL web app)
- Mention scope (adoption, incident reporting, vaccination tracking)
- Keywords: *animal welfare management, web-based system, PHP, MySQL, incident reporting*

**Manuscript (Abstract Walkthrough — Presenter 1):**

> "Our abstract opens by establishing the real-world problem: animal welfare offices in Marikina currently rely on fragmented, manual processes — leading to delayed incident response, inconsistent records, and an inefficient adoption workflow. The identified system gap is the absence of a unified, role-aware digital platform. Our proposed solution is a web-based management system built using PHP 8 and MySQL, implementing session-based authentication, a dynamic adoption workflow with status tracking, and a structured incident reporting module. The abstract is within the IEEE-compliant 150–250 word limit, written as a single paragraph with no citations. Our keywords — animal welfare management, web-based system, PHP, incident reporting, and adoption tracking — are all technical and IT-relevant."

---

### III. INTRODUCTION — 20 pts

**Sub-sections:**
- A. Background & Motivation (5 pts) — real-world IT context
- B. Problem Statement (5 pts) — clear, measurable, system/technology-oriented
- C. Objectives (5 pts) — specific, aligned, achievable
- D. Scope & Limitations (5 pts) — features, users, platforms, constraints

---

#### A. Background & Motivation

**Key points:**
- Animal welfare management in PH LGUs is largely paper-based
- Marikina City handles stray animal incidents, adoption drives, and vaccination campaigns — all without a unified digital record system
- The IT context: digitization of government services at the LGU level is a national agenda (eGov PH)
- This creates a measurable inefficiency that a web system can directly address

**Manuscript:**

> "The background section establishes that local government units handling animal welfare in the Philippines, including Marikina City, still rely heavily on manual logbooks and physical forms. This creates bottlenecks in incident response, lost adoption records, and no visibility into vaccination schedules. Our motivation is grounded in the national push for digital public services under the eGov Philippines framework, which makes this problem both timely and technically solvable using web technologies."

---

#### B. Problem Statement

**Key points:**
- Fragmented record-keeping across incidents, adoptions, and vaccinations
- No system to track the state of an animal from intake to adoption
- Residents have no way to file or monitor incident reports digitally
- Measurable: delays in response, duplicate adoption applications, missed vaccination schedules

**Manuscript:**

> "The core problem is that Marikina City's animal welfare operations currently have no unified digital platform. Specifically: incident reports are filed manually with no tracking mechanism, adoption requests are processed without deduplication controls, and vaccination schedules are managed in isolated spreadsheets. These are measurable, system-solvable problems — not abstract issues — which is exactly what makes this an appropriate BSIT capstone."

---

#### C. Objectives

**Say these confidently — panels will ask you to recite them:**

1. To design and develop a web-based animal welfare management system for Marikina City
2. To implement a structured incident reporting module with severity classification and status tracking
3. To build an adoption workflow that prevents duplicate applications through automated animal status management
4. To provide a vaccination scheduling module linked to adopted animals
5. To ensure secure user authentication with session-based access control

**Manuscript:**

> "Our objectives are specific and achievable within the scope of a third-year capstone. Objective one is the development of the system itself. Objectives two through four address the three core operational modules. Objective five addresses the security requirement. All five objectives have been achieved in the current implementation, which we will demonstrate shortly."

---

#### D. Scope & Limitations

**In scope:**
- Web-based access via browser (localhost / LAN deployment)
- Registered users: residents (report incidents, request adoptions) and staff (manage records)
- Modules: Animal Records, Adoption Request, My Adoption Requests, Incident Reporting, My Reports, Vaccination Management, Dashboard
- Platform: PHP 8.3, MySQL 8, any modern browser

**Limitations:**
- No mobile-native app (responsive web only)
- No real-time push notifications
- No payment gateway (adoption fees handled offline)
- No integration with national PAWS/DILG databases

**Manuscript:**

> "The scope of the system covers all core operational modules: animal records, adoption processing with an automated reservation system, incident reporting, and vaccination tracking. Users are authenticated — no anonymous access. The platform is browser-based, requiring PHP 8 and MySQL 8. Limitations include the absence of a mobile app, real-time notifications, and external database integration — these are identified as future work, not system failures."

---

### IV. RELATED WORK — 20 pts

**What panels check:**
- Credible scholarly and technical sources used
- Systems/studies analyzed and compared — not just listed
- Research/system gap is clearly identified

**Key points to hit:**
- Cite 3–5 related systems (e-VETS, city shelter management systems, PH LGU digitization studies)
- For each: state what it does, what it lacks, and how your system addresses that gap
- Gap statement: no existing system addresses Marikina City's specific context with all three modules unified

**Manuscript:**

> "In related work, we reviewed existing animal welfare platforms and LGU digitization studies. Systems like e-VETS and commercial shelter management platforms address vaccination and animal records but lack integrated incident reporting and an automated adoption deduplication mechanism. Studies on Philippine LGU digital transformation highlight the gap in localized government systems, particularly at the city level. Our system fills this gap by integrating all three operational domains — adoption, incidents, and vaccination — into a single, user-authenticated platform tailored for Marikina City's workflow."

**Panel trap to avoid:** Don't just say "we looked at studies and they were incomplete." Say *specifically* what feature they lacked and name the gap your system fills.

---

### V. METHODOLOGY — 25 pts ← HIGHEST WEIGHT

**What panels check:**
- Research design appropriate for IT / applied system study
- Conceptual framework or system model is clear and logical
- System overview explains purpose, components, users
- Functional and non-functional requirements are complete
- Ethical, legal, security, and technical considerations addressed

**Research Design:** Developmental Research (applied IT system development using Agile/iterative approach)

**Key points:**
- Methodology: iterative development — requirements → design → implementation → testing
- Conceptual framework: Input (user data, incident data, adoption requests) → Process (PHP backend, MySQL queries, session auth) → Output (dashboard, reports, status tracking)
- System components: 5 modules, 1 authentication layer, 1 shared design system
- Users: Residents (report, adopt), Staff/Admin (manage records, update statuses)

**Functional Requirements (know these):**
1. Users must be able to register and log in securely
2. Users can submit incident reports with location, animal details, severity
3. Users can browse animals and submit adoption requests
4. System prevents duplicate adoption requests by auto-setting animal to Reserved
5. Staff can manage animal records, vaccination schedules, and update statuses

**Non-Functional Requirements:**
- Security: bcrypt passwords, session regeneration, prepared statements
- Usability: consistent UI design system across all pages
- Performance: PHP built-in server suitable for LAN deployment
- Maintainability: modular CSS, separated PHP pages, consistent conventions

**Ethical/Legal Considerations:**
- User data (name, contact, address) is stored only for operational purposes
- Password hashing ensures credential security
- System does not collect data from minors without consent flagging

**Manuscript:**

> "Our methodology follows a developmental research design, appropriate for an applied IT system study. We used an iterative approach: we gathered functional requirements, designed the database schema and UI, implemented each module incrementally, and tested after each iteration. The conceptual framework follows an Input-Process-Output model: inputs are user registrations, incident data, and adoption applications; the process layer is our PHP 8 backend with MySQL queries and session authentication; and the outputs are the dashboard, status tracking, and report views. Functional requirements include secure authentication, incident submission, adoption workflow with deduplication, and vaccination management. Non-functional requirements cover security — specifically bcrypt hashing and prepared statements — usability through a unified design system, and maintainability through modular CSS and consistent PHP conventions. Ethical considerations include data minimization, secure credential storage, and role-appropriate access control."

---

### VI. REFERENCES — 10 pts

**What panels check:**
- IEEE citation format strictly followed
- Sources are recent (within 5–7 years), relevant, and credible (academic & technical)

**Format reminder (IEEE):**
> [1] A. Author, "Title of paper," *Journal Name*, vol. X, no. X, pp. XX–XX, Year.

**Suggested source types:**
- Philippine eGovernment digitization studies (2019–2025)
- Web application security papers (OWASP, IEEE)
- LGU animal welfare management references
- PHP/MySQL technical documentation (cite official docs as technical references)
- DILG or DA-BAI publications on animal welfare in PH

**Key point:** Panels count your citations and check the years. Aim for at least 8 sources, majority within last 5 years.

---

### VII. IEEE COMPLIANCE & WRITING QUALITY — 10 pts

**What panels check:**
- IEEE structure and logical flow observed
- Writing is formal, objective, and technically clear

**Key points:**
- Use passive voice in methodology: "The system was developed using..." not "We made it using..."
- No colloquialisms, no first-person in abstract
- Section headings match IEEE paper structure
- Figures are labeled (Fig. 1, Fig. 2) with captions below
- Tables labeled above (Table I, Table II)

---

## DEMO FLOW (Live System Walkthrough)

> Practice this sequence. Keep it under 8 minutes.

**Step 1 — Landing Page (30 sec)**
> "This is the public landing page. It explains the system's purpose to Marikina City residents before they log in."

**Step 2 — Register & Login (1 min)**
> "We register a new resident account — this uses PHP's `password_hash` with bcrypt. On login, the session is regenerated to prevent session fixation."

**Step 3 — Dashboard (1 min)**
> "The dashboard is personalized — it shows only this user's reports, adoption requests, and vaccination schedules. Quick links route to the main modules."

**Step 4 — Report an Incident (1.5 min)**
> "We fill out an incident report — location, barangay, animal type, victim details, severity level. On submit, a modal confirms the Reference ID. The data is now in the incidents table."

**Step 5 — My Reports (1 min)**
> "My Reports shows all incidents filed by this user. Clicking View opens a detail modal — all data inline, no page reload. Status badges reflect the current review stage."

**Step 6 — Adopt an Animal (1.5 min)**
> "Browse shows all animals with live status badges — Available, Reserved, Adopted. We click View Details on an Available animal, fill the adoption form, and submit. Watch — the animal card immediately shows Reserved and the Adopt button is disabled. This prevents duplicate applications."

**Step 7 — My Adoption Requests (30 sec)**
> "The request is now logged here with a Pending status. Staff can update this to Approved or Completed."

**Step 8 — Vaccinations (30 sec)**
> "Vaccination records are managed here — vaccine type, schedule date, vet staff, and status tracking."

---

## COMMON PANEL QUESTIONS & ANSWERS

**Q: Why PHP and not a framework like Laravel?**
> "For a third-year capstone, vanilla PHP 8 gives us direct control over every component, making it easier to explain, debug, and demonstrate our understanding of backend fundamentals. A framework like Laravel abstracts too much for an evaluative defense context."

**Q: How does your system prevent unauthorized access?**
> "Every protected page starts by calling session-handler.php and checking `$_SESSION['loggedIn']`. If the session is invalid, the user is immediately redirected to login.php. Sessions use HTTP-only, SameSite=Lax cookies and are regenerated on every login."

**Q: Is this system scalable for actual LGU deployment?**
> "The current implementation is designed for LAN or localhost deployment as a proof of concept. For production deployment, we identified the need for a proper web server like Apache or Nginx, HTTPS, and environment-based configuration — these are documented as future work."

**Q: What makes your system different from a spreadsheet?**
> "A spreadsheet has no access control, no workflow logic, and no automation. Our system enforces user authentication, automatically updates animal status when an adoption request is filed, and provides a real-time dashboard — none of which a spreadsheet can do without manual intervention."

**Q: Why did you choose MySQL over other databases?**
> "MySQL is the industry-standard relational database for PHP applications, well-documented, and supported by every hosting provider. The relational model is appropriate because our data — users, animals, adoptions, incidents — has clear relationships that benefit from foreign key constraints and normalized tables."

---

## RED FLAG AVOIDANCE

> These are the Common BSIT Red Flags from your rubric. Know how to counter each one.

| Red Flag | Your Counter |
|---|---|
| Purely theoretical, no applied IT solution | "We have a live, working system — let us demo it." |
| Documentation-only, no system planning | "Our methodology section documents the iterative development process, schema design, and module-by-module implementation." |
| Weak or unclear system requirements | "We have defined functional and non-functional requirements — both are covered in our methodology section." |
| No real-world IT problem identified | "The problem is real and localized: Marikina City has no unified digital platform for animal welfare operations. We can cite the manual processes still in use." |

---

## ROLE ASSIGNMENTS (Suggested)

| Member | Sections |
|---|---|
| Presenter 1 | Opening, Title, Abstract, Introduction A & B |
| Presenter 2 | Introduction C & D, Related Work |
| Presenter 3 | Methodology, System Demo |
| All | Q&A (answer questions in your domain) |

---

*Prepared for Capstone 1 Defense — February 2026*

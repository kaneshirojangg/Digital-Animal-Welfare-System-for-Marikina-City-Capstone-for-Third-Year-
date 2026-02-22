SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
CREATE TABLE `adoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `animal_name` varchar(100) NOT NULL,
  `animal_type` varchar(50) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_contact` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `animals` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `status` enum('In Shelter','Available for Adoption','Adopted','Rescued','Deceased') DEFAULT 'In Shelter',
  `intake_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(8, 'woeisme1233', '$2y$10$aiXVjSvenY5H05g0KUUt9uNr/WEzYEK/ALDI2S177bCIOJ6mEvxNe', 'tiny asa', 'Veterinarian', '2026-02-14 09:36:43');
CREATE TABLE `incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `incident_date` date NOT NULL,
  `incident_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `animal_type` varchar(50) NOT NULL,
  `animal_color` varchar(100) DEFAULT NULL,
  `animal_size` varchar(50) DEFAULT NULL,
  `animal_distinguishing_features` text DEFAULT NULL,
  `victim_name` varchar(100) NOT NULL,
  `victim_age` int(3) DEFAULT NULL,
  `victim_contact` varchar(50) DEFAULT NULL,
  `injury_description` text NOT NULL,
  `severity_level` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('New','Under Review','Resolved','Closed') DEFAULT 'New',
  `treatment_received` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `incident_date` (`incident_date`),
  KEY `severity_level` (`severity_level`),
  CONSTRAINT `fk_incidents_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `vaccinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `animal_name` varchar(100) NOT NULL,
  `vaccine_type` varchar(100) NOT NULL,
  `schedule_date` datetime NOT NULL,
  `vet_staff` varchar(100) DEFAULT NULL,
  `status` enum('Upcoming','Done','Overdue') DEFAULT 'Upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `vaccinations` (`id`, `animal_name`, `vaccine_type`, `schedule_date`, `vet_staff`, `status`, `created_at`) VALUES
(1, 'Max', 'Anti-rabies + Deworming', '2026-02-12 09:00:00', 'Dr. Ana Reyes', 'Upcoming', '2026-02-14 10:12:41'),
(2, 'Luna', '5-in-1 Booster', '2026-02-15 14:00:00', 'Dr. Reyes', 'Done', '2026-02-14 10:12:41');
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  UNIQUE KEY `uk_session_token` (`session_token`),
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `vaccinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

-- ============================================================
-- UBakrie Space — Full Database Schema & Seed Data
-- Versi Final — Juli 2026
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ============================================================
-- TABLE: users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','dosen','ormawa','baa','marketing','ga','bima','security','teknisi','admin') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (name, username, email, password, role) VALUES
('Amanda',         'Amanda',    'Amanda@student.bakrie.ac.id',    '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'mahasiswa'),
('Najwa',          'Najwa',     'Najwa@student.bakrie.ac.id',     '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'mahasiswa'),
('Ivan',           'Ivan',      'Ivan@student.bakrie.ac.id',      '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'mahasiswa'),
('Fadil',          'Fadil',     'Fadil@student.bakrie.ac.id',     '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'mahasiswa'),
('Otto Daniswara', 'Otto',      'Otto@student.bakrie.ac.id',      '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'mahasiswa'),
('Nofita',         'Nofita',    'Nofita@student.bakrie.ac.id',    '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'mahasiswa'),
('Dr. Budi Santoso','budi',     'budi@bakrie.ac.id',              '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'dosen'),
('Admin BAA',      'baa',       'baa@bakrie.ac.id',               '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'baa'),
('Admin GA',       'ga',        'ga@bakrie.ac.id',                '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'ga'),
('Admin Marketing','marketing', 'marketing@bakrie.ac.id',         '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'marketing'),
('Admin BIMA',     'bima',      'bima@bakrie.ac.id',              '$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.', 'bima');

-- ============================================================
-- TABLE: rooms
-- ============================================================
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(50) NOT NULL,
  `capacity` INT DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `is_priority_marketing` tinyint(1) DEFAULT 0,
  `status` ENUM('aktif','maintenance','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rooms` (room_name, is_priority_marketing) VALUES
('R1',1),('R2',1),('R4A',0),('R4B',0),('R5A',0),
('R5B',0),('R6A',0),('R6B',0),('R7 (Student Lounge)',0),
('R8',0),('R9',0),('R10',0),('R11',0),('Ruang Band & ORMAWA',0);

-- ============================================================
-- TABLE: inventory
-- ============================================================
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) NOT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `condition_status` VARCHAR(50) DEFAULT 'baik',
  PRIMARY KEY (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: booking
-- ============================================================
DROP TABLE IF EXISTS `booking`;
CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(150) NOT NULL,
  `organization` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `rooms` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pic` varchar(100) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `recurring_group_id` CHAR(36) DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_booking_start_datetime` (`start_datetime`),
  KEY `idx_booking_status` (`status`),
  KEY `idx_booking_recurring_group` (`recurring_group_id`),
  CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking` (booking_id, user_id, event_name, organization, phone, event_description, start_datetime, end_datetime, status) VALUES
(5,  1, 'Rapat Mingguan',       'HMTIF',      '0000000',     NULL, '2026-01-06 15:25:00', '2026-01-06 16:00:00', 'approved'),
(6,  1, 'Rapat Mingguan',       'HMTIF',      '0000000',     NULL, '2026-01-07 15:25:00', '2026-01-07 16:00:00', 'approved'),
(9,  2, 'INSTING',              'BEM',         '08888888888', NULL, '2026-10-01 09:00:00', '2026-10-01 12:00:00', 'pending'),
(10, 1, 'Tutorial',             'HMTIF',      '081211603054',NULL, '2026-01-30 10:10:00', '2026-01-30 13:00:00', 'approved'),
(11, 2, 'INSTING',              'BEM',         '08888888888', NULL, '2026-10-01 09:00:00', '2026-10-01 15:00:00', 'pending'),
(12, 5, 'Rapat Rutin Kelompok', 'Kelompok 5', '08785675746', 'Rapat mingguan kelompok 5', '2026-07-07 09:00:00', '2026-07-07 11:00:00', 'pending'),
(13, 5, 'Rapat Rutin Kelompok', 'Kelompok 5', '08785675746', 'Rapat mingguan kelompok 5', '2026-07-14 09:00:00', '2026-07-14 11:00:00', 'pending'),
(14, 5, 'Rapat Rutin Kelompok', 'Kelompok 5', '08785675746', 'Rapat mingguan kelompok 5', '2026-07-21 09:00:00', '2026-07-21 11:00:00', 'pending'),
(15, 5, 'Rapat Rutin Kelompok', 'Kelompok 5', '08785675746', 'Rapat mingguan kelompok 5', '2026-07-28 09:00:00', '2026-07-28 11:00:00', 'pending');

-- ============================================================
-- TABLE: booking_rooms
-- ============================================================
DROP TABLE IF EXISTS `booking_rooms`;
CREATE TABLE `booking_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `idx_room_id` (`room_id`),
  CONSTRAINT `booking_rooms_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_rooms` (booking_id, room_id) VALUES
(5,9),(6,9),(9,7),(10,7),(11,7),
(12,7),(13,7),(14,7),(15,7);

-- ============================================================
-- TABLE: booking_approval
-- ============================================================
DROP TABLE IF EXISTS `booking_approval`;
CREATE TABLE `booking_approval` (
  `approval_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `step` enum('baa','marketing','ga','bima','ga_final') NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `digital_signature` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`approval_id`),
  KEY `booking_id` (`booking_id`),
  KEY `approver_id` (`approver_id`),
  CONSTRAINT `booking_approval_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_approval_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- booking 5,6,10 sudah approved (BIMA → GA → selesai)
INSERT INTO `booking_approval` (booking_id, step, approver_id, status, approved_at) VALUES
(5,  'bima', 11, 'approved', '2026-01-10 10:00:00'),
(5,  'ga',   9,  'approved', '2026-01-10 11:00:00'),
(6,  'bima', 11, 'approved', '2026-01-10 10:00:00'),
(6,  'ga',   9,  'approved', '2026-01-10 11:00:00'),
(10, 'bima', 11, 'approved', '2026-01-31 10:00:00'),
(10, 'ga',   9,  'approved', '2026-01-31 11:00:00'),
-- booking pending
(9,  'bima', NULL, 'pending', NULL),
(11, 'bima', NULL, 'pending', NULL),
(12, 'bima', NULL, 'pending', NULL),
(13, 'bima', NULL, 'pending', NULL),
(14, 'bima', NULL, 'pending', NULL),
(15, 'bima', NULL, 'pending', NULL);

-- ============================================================
-- TABLE: booking_feedback
-- ============================================================
DROP TABLE IF EXISTS `booking_feedback`;
CREATE TABLE `booking_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  UNIQUE KEY `uniq_feedback_booking` (`booking_id`),
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `booking_feedback_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: booking_inventory
-- ============================================================
DROP TABLE IF EXISTS `booking_inventory`;
CREATE TABLE `booking_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `inventory_id` (`inventory_id`),
  CONSTRAINT `booking_inventory_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_inventory_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: room_inventory
-- ============================================================
DROP TABLE IF EXISTS `room_inventory`;
CREATE TABLE `room_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) DEFAULT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `inventory_id` (`inventory_id`),
  CONSTRAINT `room_inventory_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  CONSTRAINT `room_inventory_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: booking_recurrence_rule
-- ============================================================
DROP TABLE IF EXISTS `booking_recurrence_rule`;
CREATE TABLE `booking_recurrence_rule` (
  `recurring_group_id` CHAR(36) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `frequency` ENUM('daily','weekly','monthly') NOT NULL DEFAULT 'weekly',
  `interval_count` INT(11) NOT NULL DEFAULT 1,
  `day_of_week` TINYINT(1) DEFAULT NULL,
  `recurrence_start_date` DATE NOT NULL,
  `recurrence_end_date` DATE NOT NULL,
  `total_occurrences` INT(11) DEFAULT NULL,
  `generated_count` INT(11) NOT NULL DEFAULT 0,
  `failed_count` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recurring_group_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `recurrence_rule_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_recurrence_rule` (recurring_group_id, user_id, frequency, interval_count, day_of_week, recurrence_start_date, recurrence_end_date, total_occurrences, generated_count) VALUES
('135e224f-6bf4-4f88-b890-dae1c2f8f5f5', 5, 'weekly', 1, 2, '2026-07-01', '2026-07-31', 4, 4);

-- ============================================================
-- TABLE: notifications
-- ============================================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `booking_id` INT(11) DEFAULT NULL,
  `title` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notifications` (user_id, booking_id, title, message) VALUES
(5, 12, 'Peminjaman Rutin Berhasil Dibuat', 'Sebanyak 4 jadwal berhasil dibuat untuk grup rutin Anda.');

-- ============================================================
-- VIEWS
-- ============================================================
DROP VIEW IF EXISTS `vw_booking_report`;
CREATE VIEW `vw_booking_report` AS
SELECT
  b.booking_id, b.event_name, b.organization, b.status,
  b.recurring_group_id, b.start_datetime, b.end_datetime,
  TIMESTAMPDIFF(MINUTE, b.start_datetime, b.end_datetime) AS duration_minutes,
  u.user_id, u.name AS requester_name, u.role AS requester_role,
  r.room_id, r.room_name, r.location AS room_location,
  r.capacity AS room_capacity, r.is_priority_marketing
FROM `booking` b
JOIN `users` u ON u.user_id = b.user_id
JOIN `booking_rooms` br ON br.booking_id = b.booking_id
JOIN `rooms` r ON r.room_id = br.room_id;

DROP VIEW IF EXISTS `vw_room_utilization_monthly`;
CREATE VIEW `vw_room_utilization_monthly` AS
SELECT
  r.room_id, r.room_name,
  DATE_FORMAT(b.start_datetime, '%Y-%m') AS period,
  COUNT(b.booking_id) AS total_bookings,
  SUM(TIMESTAMPDIFF(MINUTE, b.start_datetime, b.end_datetime)) AS total_minutes_used
FROM `booking` b
JOIN `booking_rooms` br ON br.booking_id = b.booking_id
JOIN `rooms` r ON r.room_id = br.room_id
WHERE b.status = 'approved'
GROUP BY r.room_id, r.room_name, period;

DROP VIEW IF EXISTS `vw_my_booking_history`;
CREATE VIEW `vw_my_booking_history` AS
SELECT
  b.booking_id, b.event_name, b.organization,
  b.status AS booking_status, b.start_datetime, b.end_datetime,
  TIMESTAMPDIFF(MINUTE, b.start_datetime, b.end_datetime) AS duration_minutes,
  b.recurring_group_id, b.created_at,
  u.user_id, u.name AS requester_name, r.room_name,
  ba.step AS approval_step, ba.status AS approval_status,
  ba.notes AS approval_notes, ba.approved_at
FROM `booking` b
JOIN `users` u ON u.user_id = b.user_id
JOIN `booking_rooms` br ON br.booking_id = b.booking_id
JOIN `rooms` r ON r.room_id = br.room_id
LEFT JOIN `booking_approval` ba ON ba.booking_id = b.booking_id
  AND ba.approval_id = (
    SELECT MAX(ba2.approval_id)
    FROM `booking_approval` ba2
    WHERE ba2.booking_id = b.booking_id
  );

DROP VIEW IF EXISTS `vw_room_popularity`;
CREATE VIEW `vw_room_popularity` AS
SELECT
  r.room_id, r.room_name,
  DAYOFWEEK(b.start_datetime) AS day_of_week,
  HOUR(b.start_datetime) AS hour_of_day,
  COUNT(b.booking_id) AS booking_count
FROM `booking` b
JOIN `booking_rooms` br ON br.booking_id = b.booking_id
JOIN `rooms` r ON r.room_id = br.room_id
WHERE b.status = 'approved'
GROUP BY r.room_id, r.room_name, DAYOFWEEK(b.start_datetime), HOUR(b.start_datetime)
ORDER BY booking_count DESC;

SET FOREIGN_KEY_CHECKS = 1;
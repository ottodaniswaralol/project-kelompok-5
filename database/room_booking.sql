-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: room_booking
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(150) NOT NULL,
  `organization` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `memo_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pic` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking`
--

LOCK TABLES `booking` WRITE;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
INSERT INTO `booking` VALUES (5,1,'singkong','lawaakan','08785675746','','2026-01-03 17:54:00','2026-01-03 18:54:00',NULL,'2025-12-30 06:54:30','Bima'),(6,1,'singkong','lawaakan','08785675746','','2026-01-03 15:00:00','2026-01-03 17:00:00',NULL,'2025-12-30 06:58:28','Bima'),(7,1,'singkong','lawaakan','','','2026-01-03 18:01:00','2026-01-03 19:02:00',NULL,'2025-12-30 07:00:34','Bima'),(8,1,'singkong','lawaakan','08785675746','','2026-01-01 15:02:00','2026-01-01 18:04:00',NULL,'2025-12-30 07:01:15','Bima'),(9,1,'singkong','lawaakan','08785675746','','2026-01-04 18:05:00','2026-01-04 19:07:00',NULL,'2025-12-30 07:02:12','Bima'),(10,5,'singkong','lawaakan','08785675746','','2026-01-02 20:11:00','2026-01-02 23:05:00',NULL,'2025-12-30 07:05:27','Bima'),(11,5,'singkong','lawaakan','08785675746','','2026-01-01 20:08:00','2026-01-01 23:11:00',NULL,'2025-12-30 07:08:56','Bima'),(12,5,'singkong','lawaakan','','','2026-01-01 20:21:00','2026-01-01 23:19:00',NULL,'2025-12-30 07:15:31','Bima'),(13,5,'singkong','lawaakan','08785675746','','2026-01-10 16:40:00','2026-01-10 17:41:00',NULL,'2025-12-30 07:38:31','Bima');
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_approval`
--

DROP TABLE IF EXISTS `booking_approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_approval` (
  `approval_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `step` enum('baa','marketing','ga','bima','ga_final') NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`approval_id`),
  KEY `booking_id` (`booking_id`),
  KEY `approver_id` (`approver_id`),
  CONSTRAINT `booking_approval_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_approval_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_approval`
--

LOCK TABLES `booking_approval` WRITE;
/*!40000 ALTER TABLE `booking_approval` DISABLE KEYS */;
INSERT INTO `booking_approval` VALUES (1,5,'baa',NULL,'pending',NULL,NULL),(2,6,'baa',NULL,'pending',NULL,NULL),(3,7,'baa',NULL,'pending',NULL,NULL),(4,8,'baa',NULL,'pending',NULL,NULL),(5,9,'baa',NULL,'pending',NULL,NULL),(6,10,'baa',NULL,'pending',NULL,NULL),(7,11,'baa',NULL,'pending',NULL,NULL),(8,12,'baa',NULL,'pending',NULL,NULL),(9,13,'baa',NULL,'pending',NULL,NULL);
/*!40000 ALTER TABLE `booking_approval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_feedback`
--

DROP TABLE IF EXISTS `booking_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `booking_feedback_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_feedback`
--

LOCK TABLES `booking_feedback` WRITE;
/*!40000 ALTER TABLE `booking_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_inventory`
--

DROP TABLE IF EXISTS `booking_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_inventory`
--

LOCK TABLES `booking_inventory` WRITE;
/*!40000 ALTER TABLE `booking_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_rooms`
--

DROP TABLE IF EXISTS `booking_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `booking_rooms_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  CONSTRAINT `booking_rooms_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_rooms`
--

LOCK TABLES `booking_rooms` WRITE;
/*!40000 ALTER TABLE `booking_rooms` DISABLE KEYS */;
INSERT INTO `booking_rooms` VALUES (1,5,7),(2,6,9),(3,7,12),(4,8,7),(5,9,3),(6,10,12),(7,11,13),(8,12,5),(9,13,7),(10,14,12);
/*!40000 ALTER TABLE `booking_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) NOT NULL,
  PRIMARY KEY (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_inventory`
--

DROP TABLE IF EXISTS `room_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_inventory`
--

LOCK TABLES `room_inventory` WRITE;
/*!40000 ALTER TABLE `room_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `room_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(50) NOT NULL,
  `is_priority_marketing` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'R1',1),(2,'R2',1),(3,'R4A',0),(4,'R4B',0),(5,'R5A',0),(6,'R5B',0),(7,'R6A',0),(8,'R6B',0),(9,'R7 (Student Lounge)',0),(10,'R8',0),(11,'R9',0),(12,'R10',0),(13,'R11',0),(14,'Ruang Band & ORMAWA',0);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','dosen','ormawa','baa','marketing','ga','bima','security','teknisi','admin') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Amanda','Amanda@student.bakrie.ac.id','$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.','mahasiswa',NULL),(2,'Najwa','Najwa@student.bakrie.ac.id','$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.','mahasiswa',NULL),(3,'Ivan','Ivan@student.bakrie.ac.id.com','$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.','mahasiswa',NULL),(4,'Fadil','Fadil@student.bakrie.ac.id','$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.','mahasiswa',NULL),(5,'Otto','Otto@student.bakrie.ac.id','$2y$10$LIbbLkSsdE2nyKJFSph9h.b1pkRRbATTuH6eehvMUe9u8sgRbXaZ.','mahasiswa',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-30 16:22:22

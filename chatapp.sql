-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: localhost    Database: chatapp_sample
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `msg_id` int NOT NULL,
  `incoming_msg_id` int NOT NULL,
  `outgoing_msg_id` int NOT NULL,
  `msg` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (0,108561849,1582444253,'Tesing'),(0,1582444253,108561849,'Hey'),(0,743389383,108561849,'Test'),(0,743389383,108561849,'Testing😉'),(0,743389383,1472868720,'asd'),(0,1582444253,1472868720,'test'),(0,1582444253,1472868720,''),(0,108561849,1472868720,'Test'),(0,108561849,1472868720,'Testing 😭'),(0,1582444253,1472868720,'darkness'),(0,404966815,1472868720,'Testing'),(0,404966815,1472868720,'what you doing?'),(0,404966815,1472868720,'What you doin?'),(0,743389383,1472868720,'Testing'),(0,1582444253,1472868720,'Hey Jake');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `unique_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Jane','$2y$10$c.3y4wgVOr7NB07YZSVvxOLhtzOZt9blBRTHQ7imNXtbLbRwD5SKK','female.png','not active',108561849),(2,'Jake','$2y$10$mEdhrx0Dhloc7rOy8MAgG.NPQa2qYI3CVe9Oyno3RBG6pKzrdE7PW','male.jpeg','not active',1582444253),(3,'Hale','$2y$10$YHPivVlKAADXzzxfz3OTTuekuGKfIdgBLYw1jq5cBNTiThrnEQJ.a','female.png','not active',683105318),(4,'Jack','$2y$10$msMcTUbmw7UQOAfdGnIf2.5gurPVd5/YTcBjePsrj6EeiWIfSJflq','male.jpeg','not active',1472868720),(5,'Trump','$2y$10$jGGhqwL9wseIuf1XrFram.99EEtBjadf8VYojFNpPjHRaUjw9w3MO','male.jpeg','not active',743389383),(6,'Tatum','$2y$10$Z6Hb1Cft1Dl5Wa/.ylhAFuwUZ2.Y8Vzg0KggBBGFpD0ikNICEYWF6','male.jpeg','not active',404966815);
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

-- Dump completed on 2024-03-02  2:08:14

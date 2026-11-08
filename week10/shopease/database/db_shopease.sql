-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: db_shopease
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `recipient_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (1,3,'Bagas Saputra','081200000003','Jl. Melati Raya No. 21, Kelurahan Sukamaju, Kecamatan Cilandak','Jakarta Selatan','DKI Jakarta','12430',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `addresses` VALUES (2,4,'Nadia Rahmawati','081200000004','Jl. Cendana No. 8, Kelurahan Cigadung, Kecamatan Coblong','Bandung','Jawa Barat','40191',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Elektronik','elektronik','Perangkat audio, wearable, dan aksesori komputer untuk kebutuhan harian.',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `categories` VALUES (2,'Fashion','fashion','Pakaian kasual dan alas kaki dengan bahan yang nyaman dipakai.',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `categories` VALUES (3,'Aksesoris','aksesoris','Pelengkap gaya seperti topi, tas, dompet, dan kacamata.',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `categories` VALUES (4,'Rumah Tangga','rumah-tangga','Perlengkapan rumah dan alat tulis meja yang praktis.',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (121,'2026-11-01-000001','App\\Database\\Migrations\\CreateUsers','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (122,'2026-11-01-000002','App\\Database\\Migrations\\CreateCategories','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (123,'2026-11-01-000003','App\\Database\\Migrations\\CreateProducts','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (124,'2026-11-01-000004','App\\Database\\Migrations\\CreateAddresses','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (125,'2026-11-01-000005','App\\Database\\Migrations\\CreateCarts','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (126,'2026-11-01-000006','App\\Database\\Migrations\\CreateCartItems','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (127,'2026-11-01-000007','App\\Database\\Migrations\\CreateOrders','default','App',1794137434,1);
INSERT INTO `migrations` VALUES (128,'2026-11-01-000008','App\\Database\\Migrations\\CreateOrderItems','default','App',1794137435,1);
INSERT INTO `migrations` VALUES (129,'2026-11-01-000009','App\\Database\\Migrations\\CreatePayments','default','App',1794137435,1);
INSERT INTO `migrations` VALUES (130,'2026-11-01-000010','App\\Database\\Migrations\\CreateShipments','default','App',1794137435,1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `address_id` int unsigned DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `order_date` datetime NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `order_status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending_payment',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `orders_address_id_foreign` (`address_id`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `order_date` (`order_date`),
  CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `proof_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` enum('unpaid','submitted','verified','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unpaid',
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `order_id` (`order_id`),
  KEY `payment_status` (`payment_status`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(12,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Wireless Headphone BT-100','wireless-headphone-bt-100','Headphone Bluetooth over-ear dengan driver 40mm dan baterai tahan 24 jam.',299000.00,20,'wireless-headphone-bt-100.png','active','2026-10-21 11:30:35','2026-10-21 11:30:35');
INSERT INTO `products` VALUES (2,1,'Smart Watch Fit S2','smart-watch-fit-s2','Jam tangan pintar dengan pemantau detak jantung, penghitung langkah, dan notifikasi pesan.',449000.00,12,'smart-watch-fit-s2.png','active','2026-10-21 23:30:35','2026-10-21 23:30:35');
INSERT INTO `products` VALUES (3,1,'Speaker Bluetooth Mini','speaker-bluetooth-mini','Speaker portabel tahan air dengan suara bass yang tetap jernih.',189000.00,25,'speaker-bluetooth-mini.png','active','2026-10-22 11:30:35','2026-10-22 11:30:35');
INSERT INTO `products` VALUES (4,1,'Power Bank 10000mAh','power-bank-10000mah','Pengisi daya portabel dengan dua port USB dan indikator kapasitas.',159000.00,30,'power-bank-10000mah.png','active','2026-10-22 23:30:35','2026-10-22 23:30:35');
INSERT INTO `products` VALUES (5,1,'Keyboard Mekanik TKL','keyboard-mekanik-tkl','Keyboard mekanik tanpa numpad dengan switch tactile dan lampu latar putih.',549000.00,8,'keyboard-mekanik-tkl.png','inactive','2026-10-23 11:30:35','2026-10-23 11:30:35');
INSERT INTO `products` VALUES (6,1,'Mouse Wireless Silent','mouse-wireless-silent','Mouse nirkabel tanpa suara klik dengan sensitivitas yang dapat diatur.',129000.00,40,'mouse-wireless-silent.png','active','2026-10-23 23:30:35','2026-10-23 23:30:35');
INSERT INTO `products` VALUES (7,2,'Kaos Polos Premium','kaos-polos-premium','Kaos katun combed 30s dengan potongan regular fit untuk pemakaian harian.',89000.00,50,'kaos-polos-premium.png','active','2026-10-24 11:30:35','2026-10-24 11:30:35');
INSERT INTO `products` VALUES (8,2,'Kemeja Flanel Lengan Panjang','kemeja-flanel-lengan-panjang','Kemeja flanel motif kotak dengan bahan lembut untuk cuaca dingin.',199000.00,18,'kemeja-flanel-lengan-panjang.png','active','2026-10-24 23:30:35','2026-10-24 23:30:35');
INSERT INTO `products` VALUES (9,2,'Hoodie Oversize','hoodie-oversize','Hoodie berbahan fleece dengan potongan longgar dan kantong kanguru.',249000.00,15,'hoodie-oversize.png','active','2026-10-25 11:30:35','2026-10-25 11:30:35');
INSERT INTO `products` VALUES (10,2,'Celana Chino Slim','celana-chino-slim','Celana chino potongan slim dengan bahan stretch yang tidak mudah kusut.',219000.00,22,'celana-chino-slim.png','active','2026-10-25 23:30:35','2026-10-25 23:30:35');
INSERT INTO `products` VALUES (11,2,'Sneakers Casual Daily','sneakers-casual-daily','Sepatu sneakers ringan dengan sol karet dan alas kaki yang empuk.',349000.00,14,'sneakers-casual-daily.png','active','2026-10-26 11:30:35','2026-10-26 11:30:35');
INSERT INTO `products` VALUES (12,2,'Jaket Bomber','jaket-bomber','Jaket bomber berbahan parasut dengan lapisan dalam yang hangat.',289000.00,10,'jaket-bomber.png','inactive','2026-10-26 23:30:35','2026-10-26 23:30:35');
INSERT INTO `products` VALUES (13,3,'Topi Baseball Katun','topi-baseball-katun','Topi baseball dengan tali pengatur ukuran dan bahan katun yang menyerap keringat.',79000.00,35,'topi-baseball-katun.png','active','2026-10-27 11:30:35','2026-10-27 11:30:35');
INSERT INTO `products` VALUES (14,3,'Tas Ransel Laptop 14 Inci','tas-ransel-laptop-14-inci','Ransel dengan kompartemen laptop 14 inci, kantong botol, dan bahan tahan air.',329000.00,12,'tas-ransel-laptop-14-inci.png','active','2026-10-27 23:30:35','2026-10-27 23:30:35');
INSERT INTO `products` VALUES (15,3,'Dompet Kulit Sintetis','dompet-kulit-sintetis','Dompet lipat dengan banyak slot kartu dan ruang kartu identitas.',99000.00,45,'dompet-kulit-sintetis.png','active','2026-10-28 11:30:35','2026-10-28 11:30:35');
INSERT INTO `products` VALUES (16,3,'Kacamata Anti UV','kacamata-anti-uv','Kacamata dengan lensa anti UV400 dan frame ringan untuk aktivitas luar ruang.',139000.00,20,'kacamata-anti-uv.png','active','2026-10-28 23:30:35','2026-10-28 23:30:35');
INSERT INTO `products` VALUES (17,4,'Lampu Meja LED','lampu-meja-led','Lampu meja LED dengan tiga tingkat kecerahan dan leher yang dapat ditekuk.',119000.00,28,'lampu-meja-led.png','active','2026-10-29 11:30:35','2026-10-29 11:30:35');
INSERT INTO `products` VALUES (18,4,'Botol Minum Termal 500ml','botol-minum-termal-500ml','Botol stainless steel yang menjaga suhu minuman hingga 12 jam.',149000.00,33,'botol-minum-termal-500ml.png','active','2026-10-29 23:30:35','2026-10-29 23:30:35');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `courier` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tracking_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipment_status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipments`
--

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','customer','operator') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer',
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Aditya Pratama','admin@shopease.test','$2y$12$iyg78jTDLmixBVXkLL3YfevUNpw2jmxeZkDlPx0JBWGiR2dcuhF1W','admin','081200000001',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `users` VALUES (2,'Rina Kusuma','operator@shopease.test','$2y$12$iyg78jTDLmixBVXkLL3YfevUNpw2jmxeZkDlPx0JBWGiR2dcuhF1W','operator','081200000002',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `users` VALUES (3,'Bagas Saputra','bagas@shopease.test','$2y$12$iyg78jTDLmixBVXkLL3YfevUNpw2jmxeZkDlPx0JBWGiR2dcuhF1W','customer','081200000003',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
INSERT INTO `users` VALUES (4,'Nadia Rahmawati','nadia@shopease.test','$2y$12$iyg78jTDLmixBVXkLL3YfevUNpw2jmxeZkDlPx0JBWGiR2dcuhF1W','customer','081200000004',1,'2026-11-08 11:30:35','2026-11-08 11:30:35');
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

-- Dump completed on 2026-11-08 18:30:36

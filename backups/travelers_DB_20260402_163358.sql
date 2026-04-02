SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('1','1','System','Setup','Activity logs table created','127.0.0.1','Setup Script','2025-10-30 19:26:42');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('2','1','System','Setup','Activity logs table created','127.0.0.1','Setup Script','2025-10-30 19:30:53');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('3','5','keiths','Login','User logged in successfully','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-10-30 19:52:57');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('4','3','System','Create room','{\"name\":\"ad\",\"area\":\"2\",\"price\":\"1\",\"quantity\":\"5\",\"adult\":\"12\",\"children\":\"12\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-04 21:06:24');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('5','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: p4q01sn1hc27m235qlivoi4isd | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-04 22:35:50');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('6','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-04 22:35:50');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('7','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: rrlr0jfcq4aavo8oujv91k58ui | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 00:11:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('8','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 00:11:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('9','5','keiths','Update room','{\"entity_id\":\"3\",\"changes\":{\"description\":{\"old\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The room inclu\",\"new\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The room includes modern amenities, a cozy seating area, and an en-suite bathroom. Wake up to natural light streaming through large windows, offering a peaceful start to your day.\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 01:02:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('10','5','keiths','Update room','{\"entity_id\":\"3\",\"changes\":{\"description\":{\"old\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The room i\",\"new\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The room inclu\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 01:08:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('11','5','keiths','Create room','{\"name\":\"qwe\",\"area\":\"1\",\"price\":\"1\",\"quantity\":\"4\",\"adult\":\"22\",\"children\":\"2\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 01:27:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('12','5','keiths','Update room','{\"entity_id\":\"3\",\"changes\":{\"description\":{\"old\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The ro\",\"new\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The room i\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 01:37:40');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('13','5','keiths','Update room','{\"entity_id\":\"3\",\"changes\":{\"description\":{\"old\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. Th\",\"new\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. The ro\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 02:34:25');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('14','5','keiths','Update room','{\"entity_id\":\"3\",\"changes\":{\"description\":{\"old\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy\",\"new\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy. Th\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 02:34:37');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('15','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: 66bc83215bvi4n8c3l9957t754 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 12:42:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('16','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 12:42:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('17','5','keiths','Update room','{\"entity_id\":\"3\",\"changes\":{\"description\":{\"old\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;amp;amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and pri\",\"new\":\"Bed Type: 1 Queen Size Bed\\r\\nCapacity:\\r\\n\\r\\nMaximum: 2 Adults\\r\\n1 child under 12 can be accommodated (no extra bed)\\r\\nNot suitable for additional guests\\r\\nDescription:\\r\\nEscape to our romantic Couple&amp;amp;amp;amp;amp;#039;s Retreat, featuring a plush queen bed dressed in premium linens. This intimate space is perfect for couples seeking comfort and privacy\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 12:44:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('18','5','keiths','Update room','{\"entity_id\":\"4\",\"changes\":{\"description\":{\"old\":\"Name: Double-Deck Deluxe\\r\\nDescription: Our most spacious room featuring two double-deck double-sized beds. Ideal for groups or large families. Includes air conditioning, a private bathroom, and a work desk. Perfect for long stays.\\r\\nArea: 42 sq.m. (450 sq.ft.)\\r\\nBeds: 2 Double-Deck Double Beds (4 beds total)\\r\\nMax Guests: 6 Adults\\r\\nChildren: 3 Childre\",\"new\":\"Name: Double-Deck Deluxe\\r\\nDescription: Our most spacious room featuring two double-deck double-sized beds. Ideal for groups or large families. Includes air conditioning, a private bathroom, and a work desk. Perfect for long stays.\\r\\nArea: 42 sq.m. (450 sq.ft.)\\r\\nBeds: 2 Double-Deck Double Beds (4 beds total)\\r\\nMax Guests: 6 Adults\\r\\nChildren: 3 Children (under 12)\\r\\nPrice: \\u20b15,000 per night\"}}}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 12:56:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('19','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: 8ftkrfovkdvr9fhg3079804rdo | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 18:05:40');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('20','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 18:05:40');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('21','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: 8ftkrfovkdvr9fhg3079804rdo | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 20:19:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('22','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36','2025-11-06 20:19:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('23','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: cuorkbf5ph6aih39v1e50ivj31 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 21:06:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('24','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 21:06:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('25','0','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:14:00');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('26','3','System','Security: Login Failed - Invalid Password','Failed password attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:14:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('27','3','System','Security: Login Failed - Invalid Password','Failed password attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:14:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('28','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: cuorkbf5ph6aih39v1e50ivj31 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:14:49');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('29','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:14:49');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('30','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: cuorkbf5ph6aih39v1e50ivj31 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:16:40');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('31','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-13 22:16:40');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('32','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: oaa0i50hg5q86qema7qmdbp77g | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 14:51:46');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('33','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 14:51:46');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('34','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: koec9cgs1ks5pbr4n564vovg0i | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 21:15:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('35','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 21:15:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('36','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: s363ti7ll1bposdt78qqa3h3hm | IP: 127.0.0.1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 22:13:44');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('37','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 22:13:44');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('38','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: nkrkpmobboq87dkgspd7vc5b4g | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 22:30:42');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('39','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-25 22:30:42');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('40','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: m2r938av1dr7omqauh5fg3gfnf | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-26 15:43:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('41','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-26 15:43:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('42','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: m2r938av1dr7omqauh5fg3gfnf | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-26 16:09:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('43','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-26 16:09:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('44','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: m2r938av1dr7omqauh5fg3gfnf | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-26 17:28:31');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('45','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-26 17:28:31');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('46','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: m2r938av1dr7omqauh5fg3gfnf | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-27 17:40:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('47','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-11-27 17:40:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('48','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:42:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('49','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:42:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('50','0','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:47:51');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('51','3','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:48:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('52','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:48:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('53','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:48:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('54','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:09');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('55','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:10');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('56','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:10');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('57','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:12');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('58','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('59','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"1\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:32');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('60','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"1\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('61','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"1\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('62','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"1\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('63','2','Keith Eimreh Lara','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"1\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:50:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('64','2','Keith Eimreh Lara','Create room','{\"name\":\"xc\",\"area\":\"12\",\"price\":\"12\",\"quantity\":\"121\",\"adult\":\"1\",\"children\":\"2\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 13:51:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('65','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 17:10:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('66','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 17:10:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('67','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:36:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('68','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:36:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('69','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:39:23');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('70','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:39:23');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('71','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:40:37');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('72','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:40:37');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('73','0','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:48:31');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('74','3','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:57:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('75','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: cgkbq65a1o6koi21ofn03q6adh | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:57:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('76','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','2025-12-01 18:57:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('77','3','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 19:57:35');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('78','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: b3hlnkths5eq96l0hm0ko1pa9r | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 19:57:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('79','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-05 19:57:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('80','0','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:01:07');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('81','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: enhhosn2ldp4ndq475l9v6eaqo | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:01:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('82','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:01:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('83','0','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:12:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('84','0','System','Login Failed - Account Inactive','Authentication: Inactive account login attempt for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:12:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('85','5','keiths','Login Success','Authentication: User ID: 5 (kellara@gmail.com) logged in successfully | Session: enhhosn2ldp4ndq475l9v6eaqo | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:12:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('86','5','keiths','Session Started','New session created for user ID: 5 (kellara@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-18 16:12:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('87','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: kkcv7ptv31mv3hpkaidhoonej2 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-03 16:11:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('88','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-03 16:11:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('89','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: 82velokhlqlh40us37uuuaad2v | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-03 19:23:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('90','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-03 19:23:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('91','3','Admin','archive_user_query','Archived user query sr_no=17','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 19:38:11');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('92','3','Admin','archive_user_query','Archived user query sr_no=11','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 20:05:08');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('93','3','Admin','archive_delete_room','Permanently deleted archived room id=11','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 15:43:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('94','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: r7vtj6qmpkbu71oes16nl3ssr4 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 16:09:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('95','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 16:09:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('96','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: r7vtj6qmpkbu71oes16nl3ssr4 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 16:13:58');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('97','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 16:13:58');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('98','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: r7vtj6qmpkbu71oes16nl3ssr4 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 16:37:41');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('99','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-12 16:37:41');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('100','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: 6p85f6hq3v7014pvj3jhf9u02p | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-16 17:56:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('101','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-16 17:56:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('102','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('103','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('104','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('105','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('106','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('107','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('108','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('109','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('110','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('111','3','Admin','System: Error: Failed to update room status','{\"toggle_status\":\"3\",\"value\":\"0\",\"room_id\":null}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 19:26:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('112','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: 9dp0nt5k1c0lkejq3f802h9k73 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 20:18:34');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('113','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 20:18:34');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('114','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: 9dp0nt5k1c0lkejq3f802h9k73 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 20:48:08');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('115','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 20:48:08');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('116','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: vl8o9oalvdljeablrvu9bqsu5r | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:30:20');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('117','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:30:20');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('118','8','jojojo','Login Success','Authentication: User ID: 8 (jojojo@gmail.com) logged in successfully | Session: vl8o9oalvdljeablrvu9bqsu5r | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:41:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('119','8','jojojo','Session Started','New session created for user ID: 8 (jojojo@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:41:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('120','0','System','Security: Failed Login - Invalid Credentials','Email/Phone: jojojo@gmail.com | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:41:54');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('121','3','Admin','Security: Failed Login - Invalid Credentials','Email/Phone: jojojo@gmail.com | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:52:09');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('122','2','Keith Eimreh Lara','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: qehop5bein6pd154nilvrr9vd4 | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:54:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('123','2','Keith Eimreh Lara','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-23 15:54:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('124','0','System','Login Success','Authentication: User ID: 9 (keitheimreh1111@gmail.com) logged in successfully | Session: u5t4bv9j1qrsh8n5mc4baa1cdj | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 16:39:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('125','0','System','Session Started','New session created for user ID: 9 (keitheimreh1111@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 16:39:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('126','3','Admin','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: u5t4bv9j1qrsh8n5mc4baa1cdj | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 19:12:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('127','3','Admin','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 19:12:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('128','3','Admin','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: u5t4bv9j1qrsh8n5mc4baa1cdj | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 21:23:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('129','3','Admin','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 21:23:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('130','3','Admin','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: u5t4bv9j1qrsh8n5mc4baa1cdj | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 22:25:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('131','3','Admin','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-31 22:25:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('132','3','Admin','Login Success','Authentication: User ID: 2 (Kellara0227@gmail.com) logged in successfully | Session: msc9ou6hdgibrjp7re8v2nt9jc | IP: ::1 | User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-02 15:24:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('133','3','Admin','Session Started','New session created for user ID: 2 (Kellara0227@gmail.com)','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-02 15:24:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('134','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: 76m5deh3uvnvptkube87b7594r | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:33:49');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('135','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:33:49');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('136','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: 2b3jpm7iem1ibh0q8l6d4921sj | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:37:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('137','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:37:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('138','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: 59srjbl7gofunqbo7dnk8ve2a2 | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:37:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('139','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:37:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('140','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: fkajk58f48o7a4pb26j43l861p | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:37:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('141','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:37:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('142','0','System','Login Success','Authentication: User ID: 11 (codexblock1775144290@gmail.com) logged in successfully | Session: 9m47r2c1nhgahpgdslefnuebjq | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:38:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('143','0','System','Session Started','New session created for user ID: 11 (codexblock1775144290@gmail.com)','::1','curl/8.18.0','2026-04-02 15:38:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('144','0','System','Login Success','Authentication: User ID: 11 (codexblock1775144290@gmail.com) logged in successfully | Session: o5kuod48bjiaemrqcd13di50ej | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:38:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('145','0','System','Session Started','New session created for user ID: 11 (codexblock1775144290@gmail.com)','::1','curl/8.18.0','2026-04-02 15:38:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('146','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: jt0ekrbc0pfi66v71d5ka4e315 | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:39:04');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('147','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:39:04');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('148','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: f9pge1o60qqfgu7ajsbslcgjub | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:39:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('149','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:39:18');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('150','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: qpc0fk1vq103rfojq4u5ngf3if | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:40:35');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('151','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:40:35');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('152','5','codex_admin_1775144483','archive_user','Archived user id=12, email=codex_archive_guest_1775144483@example.com','::1','curl/8.18.0','2026-04-02 15:42:22');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('153','5','codex_admin_1775144483','archive_restore_user','Restored archived user id=12','::1','curl/8.18.0','2026-04-02 15:43:46');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('154','0','System','Login Success','Authentication: User ID: 10 (codexqa1775143939@gmail.com) logged in successfully | Session: 915sf7t5ks5m27vk4ceknr8jq4 | IP: ::1 | User-Agent: curl/8.18.0','::1','curl/8.18.0','2026-04-02 15:46:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('155','0','System','Session Started','New session created for user ID: 10 (codexqa1775143939@gmail.com)','::1','curl/8.18.0','2026-04-02 15:46:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('156','3','Admin','archive_delete_room','Permanently deleted archived room id=33','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-02 15:57:03');
INSERT INTO `activity_logs` (`id`,`user_id`,`user_name`,`action`,`details`,`ip_address`,`user_agent`,`created_at`) VALUES ('157','3','Admin','archive_delete_room','Permanently deleted archived room id=32','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-04-02 15:57:06');

DROP TABLE IF EXISTS `admin_cred`;
CREATE TABLE `admin_cred` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(150) NOT NULL,
  `admin_pass` varchar(150) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_cred` (`sr_no`,`admin_name`,`admin_pass`) VALUES ('2','keith','123\r\n');
INSERT INTO `admin_cred` (`sr_no`,`admin_name`,`admin_pass`) VALUES ('3','kit','123');

DROP TABLE IF EXISTS `admin_user_permissions`;
CREATE TABLE `admin_user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_user_id` int(11) NOT NULL,
  `permission_code` varchar(80) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_admin_permission` (`admin_user_id`,`permission_code`),
  KEY `permission_code` (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_admin_users_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_users` (`id`,`username`,`password`,`role`,`created_at`,`email`,`reset_token`,`reset_expires`) VALUES ('2','Staff','$2y$10$fOHSaC98CCG5hxgUwHgQvu1AlMnU4fJ6AmKnbl0eirK6z./x22OVy','staff','2026-03-06 18:27:01',NULL,NULL,NULL);
INSERT INTO `admin_users` (`id`,`username`,`password`,`role`,`created_at`,`email`,`reset_token`,`reset_expires`) VALUES ('3','Admin','$2y$10$D1fMUmk/..JNMgoSugKIf.reb0ft0gKbE8vNf3SoEhv5ZgPvKWZ62','admin','2026-03-06 18:27:14',NULL,NULL,NULL);
INSERT INTO `admin_users` (`id`,`username`,`password`,`role`,`created_at`,`email`,`reset_token`,`reset_expires`) VALUES ('4','qwe','$2y$10$s/ECMwmEIn5zpc6BcCoYBuG0ZmINaDsOkDtLKk7NoTddn9FT2aURG','staff','2026-03-06 20:03:55',NULL,NULL,NULL);

DROP TABLE IF EXISTS `archived_booking_details`;
CREATE TABLE `archived_booking_details` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL,
  `booking_note` text DEFAULT NULL,
  `staff_note` text DEFAULT NULL,
  `extras_total` decimal(10,2) DEFAULT 0.00,
  `downpayment` decimal(10,2) DEFAULT 0.00,
  `remaining_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `archived_booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('34','34','Family Room','2200','2200','3','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `archived_booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('7','7','Supreme deluxe room','900','900',NULL,'Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `archived_booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('900001','900001','Simple Room','300','300','1','Keith Eimreh Lara','123','ad',NULL,NULL,'0.00','0.00','0.00');

DROP TABLE IF EXISTS `archived_booking_order`;
CREATE TABLE `archived_booking_order` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival` int(11) NOT NULL DEFAULT 0,
  `refund` int(11) DEFAULT NULL,
  `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `trans_amt` int(11) NOT NULL,
  `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
  `trans_resp_msg` varchar(200) DEFAULT NULL,
  `rate_review` int(11) DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','partial','paid') DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `refund_proof` varchar(255) DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `confirmed_at` datetime DEFAULT NULL,
  `total_amt` decimal(10,2) DEFAULT 0.00,
  `downpayment` decimal(10,2) DEFAULT 0.00,
  `balance_due` decimal(10,2) DEFAULT 0.00,
  `promo_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `archived_booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`archived_at`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('34','5','15','2025-11-06','2025-11-07','0','1','cancelled','ORD_59083823','TEST_88751','2200','TXN_SUCCESS','TEST MODE',NULL,'2025-11-06 18:06:10','0000-00-00 00:00:00','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `archived_booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`archived_at`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('7','2','6','2022-07-29','2022-07-30','0','1','cancelled','ORD_24272313',NULL,'0','pending',NULL,NULL,'2022-07-29 01:13:42','0000-00-00 00:00:00','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `archived_booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`archived_at`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('900001','2','3','2022-07-20','2022-07-21','1','1','cancelled','TEST_CANCEL_900001',NULL,'0','pending',NULL,'0','2022-07-20 01:50:12','2026-04-02 15:12:52','pending',NULL,'uploads/refund_proofs/refund_900001_1775115348.jpg','0.00','0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');

DROP TABLE IF EXISTS `archived_ratings_reviews`;
CREATE TABLE `archived_ratings_reviews` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `archived_room_facilities`;
CREATE TABLE `archived_room_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `facilities_id` (`facilities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `archived_room_features`;
CREATE TABLE `archived_room_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `features_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `features_id` (`features_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `archived_room_images`;
CREATE TABLE `archived_room_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `archived_room_images` (`id`,`room_id`,`image`,`thumb`) VALUES ('1','16','IMG_62603.jpg','1');
INSERT INTO `archived_room_images` (`id`,`room_id`,`image`,`thumb`) VALUES ('2','34','single.jpg\r\n','0');
INSERT INTO `archived_room_images` (`id`,`room_id`,`image`,`thumb`) VALUES ('3','42','IMG_62603.jpg','1');
INSERT INTO `archived_room_images` (`id`,`room_id`,`image`,`thumb`) VALUES ('4','45','IMG_62603.jpg','1');

DROP TABLE IF EXISTS `archived_rooms`;
CREATE TABLE `archived_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `removed` tinyint(4) NOT NULL DEFAULT 1,
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('7','8','ASD','123','234','2','2','22','SDF','1','1','2025-10-30 20:35:49','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('10','5','Family Room','600','3600','7','2','2','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','1','2025-10-30 20:38:38','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('25','10','sad','2','12','2','21','21','12','0','1','2025-10-30 21:14:17','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('26','10','sad','2','12','2','21','21','12','0','1','2025-10-30 21:14:22','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('27','10','sad','2','12','2','21','21','12','0','1','2025-10-30 21:14:28','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('28','11','ffgg','22','12','1','2','12','sdf','0','1','2025-10-30 21:15:40','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('29','12','sdddd','12','12','1','123','123','sdfa','0','1','2025-10-30 21:38:31','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('30','13','dv','2','12','2','123','123','dfvds','0','1','2025-10-30 21:50:39','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('31','14','dwf','1','12','1','12','2','sdf','0','1','2025-11-04 15:06:56','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('34','1','simple room','159','58','56','12','2','asdf asd','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('35','2','simple room 2','785','159','85','452','10','adfasdfa sd','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('36','2','simple room 2','785','159','85','452','10','adfasdfa sd','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('37','6','Supreme deluxe room','500','900','12','9','10','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('38','6','Supreme deluxe room','500','900','12','9','10','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('39','7','asc','23','2','1','1','1','f','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('40','7','asc','23','2','1','1','1','f','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('41','9','asd','12','123','2','123','2','123d','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('42','16','qwe','1','1','15','22','2','12sd','0','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('43','9','asd','12','123','2','123','2','123d','1','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('44','17','xc','12','12','121','1','2','SDF','0','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('45','16','qwe','1','1','15','22','2','12sd','0','1','2026-04-02 16:12:36','1');
INSERT INTO `archived_rooms` (`id`,`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('46','17','xc','12','12','121','1','2','SDF','0','1','2026-04-02 16:12:36','1');

DROP TABLE IF EXISTS `archived_user_cred`;
CREATE TABLE `archived_user_cred` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `verification_code` varchar(255) DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `t_expire` date DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  `profile` varchar(100) DEFAULT NULL,
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `booking_details`;
CREATE TABLE `booking_details` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL,
  `booking_note` text DEFAULT NULL,
  `staff_note` text DEFAULT NULL,
  `extras_total` decimal(10,2) DEFAULT 0.00,
  `downpayment` decimal(10,2) DEFAULT 0.00,
  `remaining_balance` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`sr_no`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=900004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('1','1','Simple Room','300','300','1','Keith Eimreh Lara','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('2','2','Simple Room','300','600','a2','Kobesakol','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('3','3','Simple Room','300','1800',NULL,'Lebron Tagalog','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('4','4','Supreme deluxe room','900','4500',NULL,'Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('5','5','Supreme deluxe room','900','900',NULL,'Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('6','6','Supreme deluxe room','900','7200','52','Keith','12323432','ad2342343',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('7','7','Supreme deluxe room','900','900',NULL,'Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('8','8','Simple Room','300','600',NULL,'Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('9','9','Luxury Room','600','3000','159A','Keith Lara\r\n','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('10','10','Luxury Room','600','1800','15S','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('11','11','Supreme deluxe room','900','2700','1','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('12','12','Simple Room','300','1200','2','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('13','13','Deluxe Room','500','3000','23','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('14','14','Luxury Room','600','2400','44','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('15','15','Luxury Room','600','1200',NULL,'Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('16','16','Luxury Room','600','1200','1','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('17','17','Simple Room','300','900','20A','Keith','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('18','18','Family Room','600','1800','5','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('19','19','Couple Room','1200','2400','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('20','20','Family Room','3600','3600','5','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('21','21','Deluxe Room','1800','1800','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('22','22','Couple Room','1200','4800',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('23','23','Couple Room','1200','36000','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('24','24','Family Room','3600','14400',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('25','25','Family Room','3600','3600','2A','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('26','26','Deluxe Room','1800','1800',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('27','27','Family Room','3600','3600','3A','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('28','28','Couple Room','5000','5000','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('29','29','Couple Room','5000','5000','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('30','30','Deluxe Room','1800','1800','4','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('31','31','Deluxe Room','1800','14400','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('32','32','Double-Decker Deluxe','1800','1800','5','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('33','33','Couple Room','1500','1500','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('34','34','Family Room','2200','2200','3','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('35','35','Couple Room','1800','1800','3','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('36','36','Couple Room','1800','1800','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('37','37','Couple Room','1800','1800','3','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('38','38','Double-Decker Deluxe','4500','4500','5','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('39','39','Family Room','2200','2200','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('40','40','Couple Room','1800','1800','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('41','41','Family Room','2200','2200','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('42','42','Couple Room','1800','1800','4','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('43','43','Couple Room','1800','1800','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('44','44','Couple Room','1800','1800','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('45','45','Double-Decker Deluxe','4500','4500','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('46','46','Double-Decker Deluxe','4500','4500','3','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('47','47','Double-Decker Deluxe','4500','4500','6','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('48','48','Couple Room','1800','1800','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('49','49','Couple Room','1800','1800','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('50','50','Couple Room','1800','1800','4','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('51','51','Couple Room','1800','1800','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('52','52','Couple Room','1800','1800','4','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('53','53','Couple Room','1800','1800','2','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('54','54','Family Room','2200','2200','4','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('55','55','Double-Decker Deluxe','4500','4500','3','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('56','56','Family Room','2200','2200','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('57','57','Couple Room','1800','1800','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('58','58','Family Room','2200','2200','2','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('59','59','Family Room','2200','13200','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('60','60','Family Room','2200','2200','5','keiths','123454','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('61','61','Couple Room','1800','1800','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('62','62','Double-Decker Deluxe','4500','4500','4','Keith Eimreh Lara','09916057372','San Marcelino, Zambales','pogi ya',NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('63','63','Couple Room','1800','1800','4','Keith Eimreh Lara','09916057372','San Marcelino, Zambales','asdfsdf',NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('64','64','Couple Room','1800','1800','1','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,'wdsdv','0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('65','65','Couple Room','1800','1800','5','Keith Eimreh Lara','09916057372','San Marcelino, Zambales','ADSFSDBCJASDC','IIIIIII','0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('66','66','Couple Room','1800','1800','3','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('67','67','Couple Room','1800','1800','6','Keith Eimreh Lara','09916057372','San Marcelino, Zambales','dffgh',NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('68','68','Couple Room','1800','1800','10','Keith Eimreh Lara','09916057372','San Marcelino, Zambales','1231231',NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('69','69','Couple Room','1800','2400','3','Keith Eimreh Lara','123','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'600.00','1200.00','1200.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('70','70','Couple Room','1800','4150','3','Keith Eimreh Lara','123','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'2350.00','2075.00','2075.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('71','71','Couple Room','1800','9000','7','Keith Eimreh Lara','123','Brgy. La Paz, San Marcelino, Zambales',NULL,NULL,'1800.00','4500.00','4500.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('72','72','Couple Room','1800','4200','4','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'600.00','2100.00','2100.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('900001','900001','Simple Room','300','300','1','Keith Eimreh Lara','123','ad',NULL,NULL,'0.00','0.00','0.00');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ('900002','900002','Couple Room','1800','2175','7','Keith Eimreh Lara','09916057372','San Marcelino, Zambales',NULL,NULL,'375.00','1088.00','1087.00');

DROP TABLE IF EXISTS `booking_extras`;
CREATE TABLE `booking_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `extra_id` (`extra_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('1','70','1','Breakfast','3','150.00','450.00','2026-03-31 16:46:01');
INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('2','70','2','Airport Transfer','2','500.00','1000.00','2026-03-31 16:46:01');
INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('3','70','3','Spa Access','3','300.00','900.00','2026-03-31 16:46:01');
INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('4','71','1','Breakfast','3','150.00','1800.00','2026-03-31 16:48:22');
INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('5','72','1','Extra Matress','1','300.00','600.00','2026-03-31 22:50:16');
INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('6','900002','1','Extra Matress','1','300.00','300.00','2026-04-02 15:25:29');
INSERT INTO `booking_extras` (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`) VALUES ('7','900002','4','Extra Blanket','1','75.00','75.00','2026-04-02 15:25:29');

DROP TABLE IF EXISTS `booking_history`;
CREATE TABLE `booking_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `actor_type` varchar(30) NOT NULL DEFAULT 'system',
  `actor_id` int(11) DEFAULT NULL,
  `actor_name` varchar(150) DEFAULT NULL,
  `event_type` varchar(60) NOT NULL,
  `title` varchar(180) NOT NULL,
  `details` text DEFAULT NULL,
  `meta_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `event_type` (`event_type`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('1','68','admin','3','Admin','support_opened','Guest opened a support request','mag book sana kol',NULL,'2026-03-31 21:40:21');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('2','68','admin','3','Admin','support_reply','Support team replied','Thank you for your payment submission. We need a clearer screenshot or reference number to verify it properly. Please reply here and attach the updated proof.',NULL,'2026-03-31 21:41:27');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('3','68','admin','3','Admin','support_reply','Guest replied to support','olats sayo oma',NULL,'2026-03-31 22:43:58');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('4','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Pending.',NULL,'2026-03-31 22:44:16');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('5','68','admin','3','Admin','support_reply','Guest replied to support','olats sayo oma',NULL,'2026-03-31 22:48:12');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('6','72','admin','3','Admin','booking_created','Booking submitted','Booking request was submitted and is now awaiting admin confirmation.','{\"order_id\":\"ORD_29336440\",\"room_name\":\"Couple Room\",\"nights\":2,\"promo_code\":\"\",\"discount_amount\":0}','2026-03-31 22:50:16');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('7','72','admin','3','Admin','booking_confirmed','Booking confirmed','Admin confirmed the booking.',NULL,'2026-03-31 22:52:04');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('8','900002','admin','3','Admin','booking_created','Booking submitted','Booking request was submitted and is now awaiting admin confirmation.','{\"order_id\":\"ORD_23158966\",\"room_name\":\"Couple Room\",\"nights\":1,\"promo_code\":\"\",\"discount_amount\":0}','2026-04-02 15:25:29');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('9','57','admin','3','Admin','refund_processed','Refund processed','Refund was processed and proof of refund was uploaded.',NULL,'2026-04-02 15:35:16');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('10','60','admin','3','Admin','refund_processed','Refund processed','Refund was processed and proof of refund was uploaded.',NULL,'2026-04-02 15:35:27');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('11','65','admin','3','Admin','refund_processed','Refund processed','Refund was processed and proof of refund was uploaded.',NULL,'2026-04-02 15:35:37');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('12','900001','admin','3','Admin','refund_processed','Refund processed','Refund was processed and proof of refund was uploaded.',NULL,'2026-04-02 15:35:48');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('13','900002','admin','3','Admin','booking_confirmed','Booking confirmed','Admin confirmed the booking.',NULL,'2026-04-02 15:36:10');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('14','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Resolved.',NULL,'2026-04-02 15:36:51');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('15','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Pending.',NULL,'2026-04-02 15:36:55');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('16','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Open.',NULL,'2026-04-02 15:36:59');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('17','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Escalated.',NULL,'2026-04-02 15:37:02');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('18','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Resolved.',NULL,'2026-04-02 15:37:05');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('19','900003','guest','10','Codex QA User','payment_proof_uploaded','Payment proof re-uploaded','Guest uploaded a new payment proof for review.',NULL,'2026-04-02 15:40:35');
INSERT INTO `booking_history` (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`) VALUES ('20','68','admin','3','Admin','support_status','Support ticket status updated','Ticket #1 was moved to Resolved.',NULL,'2026-04-02 15:50:06');

DROP TABLE IF EXISTS `booking_order`;
CREATE TABLE `booking_order` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival` int(11) NOT NULL DEFAULT 0,
  `refund` int(11) DEFAULT NULL,
  `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `trans_amt` int(11) NOT NULL,
  `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
  `trans_resp_msg` varchar(200) DEFAULT NULL,
  `rate_review` int(11) DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `payment_status` enum('pending','partial','paid') DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `refund_proof` varchar(255) DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `confirmed_at` datetime DEFAULT NULL,
  `total_amt` decimal(10,2) DEFAULT 0.00,
  `downpayment` decimal(10,2) DEFAULT 0.00,
  `balance_due` decimal(10,2) DEFAULT 0.00,
  `promo_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `booking_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`),
  CONSTRAINT `booking_order_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=900004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('1','2','3','2022-07-20','2022-07-21','1',NULL,'booked','ORD_21055700',NULL,'0','pending',NULL,'0','2022-07-20 01:50:12','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('2','2','3','2022-07-20','2022-07-22','1',NULL,'booked','ORD_24215693','20220720111212800110168128204225279','600','TXN_SUCCESS','Txn Success',NULL,'2022-07-20 02:14:44','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('3','2','3','2022-07-20','2022-07-26','0','1','cancelled','ORD_26312547','20220720111212800110168165603901976','1800','TXN_SUCCESS','Txn Success',NULL,'2022-07-20 02:19:00','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('4','2','6','2022-07-20','2022-07-25','0',NULL,'payment failed','ORD_28394638','20220720111212800110168372503893816','4500','TXN_FAILURE','Your payment has been declined by your bank. Please try again or use a different method to complete the payment.',NULL,'2022-07-20 02:30:52','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('5','2','6','2022-07-20','2022-07-21','0','1','cancelled','ORD_22877860','20220720111212800110168627705312020','900','TXN_SUCCESS','Txn Success',NULL,'2022-07-20 02:32:09','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('6','2','6','2022-07-20','2022-07-28','1',NULL,'booked','ORD_28689687','20220720111212800110168303704048087','7200','TXN_SUCCESS','Txn Success','1','2022-07-20 02:34:46','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('7','2','6','2022-07-29','2022-07-30','0','1','cancelled','ORD_24272313',NULL,'0','pending',NULL,NULL,'2022-07-29 01:13:42','1','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('8','2','3','2022-08-14','2022-08-16','0','1','cancelled','ORD_22541670','20220814111212800110168092803967754','600','TXN_SUCCESS','Txn Success',NULL,'2022-08-14 01:16:05','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('9','2','5','2022-08-15','2022-08-20','1',NULL,'booked','ORD_25267746','20220815111212800110168656003990120','3000','TXN_SUCCESS','Txn Success','1','2022-08-15 19:32:05','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('10','2','5','2022-08-18','2022-08-21','1',NULL,'booked','ORD_27668816','20220815111212800110168905703947446','1800','TXN_SUCCESS','Txn Success','1','2022-08-15 19:32:58','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('11','2','6','2022-08-20','2022-08-23','1',NULL,'booked','ORD_25750549','20220820111212800110168431303975409','2700','TXN_SUCCESS','Txn Success','1','2022-08-20 00:19:57','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('12','2','3','2022-08-20','2022-08-24','1',NULL,'booked','ORD_2445093','20220820111212800110168173403969278','1200','TXN_SUCCESS','Txn Success','1','2022-08-20 00:20:23','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('13','2','4','2022-08-20','2022-08-26','1',NULL,'booked','ORD_29233995','20220820111212800110168584503978338','3000','TXN_SUCCESS','Txn Success','1','2022-08-20 00:20:45','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('14','2','5','2022-08-20','2022-08-24','1',NULL,'booked','ORD_28902800','20220820111212800110168816503988359','2400','TXN_SUCCESS','Txn Success','1','2022-08-20 00:21:06','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('15','2','5','2022-08-25','2022-08-27','0','1','cancelled','ORD_2240367','20220825111212800110168807304010818','1200','TXN_SUCCESS','Txn Success',NULL,'2019-08-21 01:51:28','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('16','2','5','2022-08-26','2022-08-28','1',NULL,'booked','ORD_28784829','20220825111212800110168627505415606','1200','TXN_SUCCESS','Txn Success','1','2022-08-25 01:52:04','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('17','2','3','2022-09-08','2022-09-11','1',NULL,'booked','ORD_21289330','20220908111212800110168809204050263','900','TXN_SUCCESS','Txn Success','0','2022-09-08 01:15:30','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('18','5','5','2025-08-26','2025-08-29','1',NULL,'booked','ORD_54483360',NULL,'0','pending',NULL,'0','2025-08-26 18:55:14','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('19','5','3','2025-08-28','2025-08-30','1',NULL,'booked','ORD_58527336',NULL,'0','pending',NULL,'0','2025-08-28 12:25:57','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('20','5','5','2025-08-28','2025-08-29','1',NULL,'booked','ORD_53073650',NULL,'0','pending',NULL,'0','2025-08-28 17:43:39','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('21','5','4','2025-09-09','2025-09-10','1',NULL,'booked','ORD_57915932','TEST_16602','1800','TXN_SUCCESS','TEST MODE','1','2025-09-09 19:06:59','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('22','5','3','2025-09-09','2025-09-13','0','1','cancelled','ORD_5726174','TEST_85066','4800','TXN_SUCCESS','TEST MODE',NULL,'2025-09-09 19:17:45','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('23','5','3','2025-09-09','2025-10-09','1','1','cancelled','ORD_53191558','TEST_96727','36000','TXN_SUCCESS','TEST MODE','0','2025-09-09 19:25:11','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('24','5','5','2025-09-15','2025-09-19','0','1','cancelled','ORD_57822107','TEST_47934','14400','TXN_SUCCESS','TEST MODE',NULL,'2025-09-15 18:01:40','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('25','5','5','2025-09-15','2025-09-16','1',NULL,'booked','ORD_52873868','TEST_95898','3600','TXN_SUCCESS','TEST MODE','1','2025-09-15 20:09:59','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('26','5','4','2025-09-15','2025-09-16','0','1','cancelled','ORD_53128418','TEST_70700','1800','TXN_SUCCESS','TEST MODE',NULL,'2025-09-15 20:12:06','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('27','5','5','2025-09-15','2025-09-16','1',NULL,'booked','ORD_59693908','TEST_31266','3600','TXN_SUCCESS','TEST MODE','1','2025-09-15 20:16:17','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('28','5','3','2025-10-13','2025-10-14','1',NULL,'booked','ORD_52469060','TEST_79249','5000','TXN_SUCCESS','TEST MODE','0','2025-10-13 19:18:24','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('29','5','3','2025-10-13','2025-10-14','0','1','cancelled','ORD_52593510','TEST_59678','5000','TXN_SUCCESS','TEST MODE',NULL,'2025-10-13 20:28:20','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('30','5','4','2025-10-30','2025-10-31','1',NULL,'booked','ORD_54169613','TEST_95122','1800','TXN_SUCCESS','TEST MODE','0','2025-10-30 19:53:10','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('31','5','4','2025-11-04','2025-11-12','1',NULL,'booked','ORD_51038826','TEST_53808','14400','TXN_SUCCESS','TEST MODE','0','2025-11-04 22:36:07','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('32','5','4','2025-11-06','2025-11-07','1',NULL,'booked','ORD_57191184','TEST_26976','1800','TXN_SUCCESS','TEST MODE','0','2025-11-06 02:35:35','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('33','5','3','2025-11-06','2025-11-07','1',NULL,'booked','ORD_57430470','TEST_25579','1500','TXN_SUCCESS','TEST MODE','0','2025-11-06 12:43:08','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('34','5','15','2025-11-06','2025-11-07','0','1','cancelled','ORD_59083823','TEST_88751','2200','TXN_SUCCESS','TEST MODE',NULL,'2025-11-06 18:06:10','1','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('35','5','3','2025-11-06','2025-11-07','1',NULL,'booked','ORD_5342957','TEST_55849','1800','TXN_SUCCESS','TEST MODE','0','2025-11-06 20:20:01','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('36','5','3','2025-11-13','2025-11-14','1',NULL,'booked','ORD_57196124','TEST_67928','1800','TXN_SUCCESS','TEST MODE','0','2025-11-13 21:24:11','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('37','2','3','2025-11-13','2025-11-14','1',NULL,'booked','ORD_22950297','TEST_70309','1800','TXN_SUCCESS','TEST MODE','0','2025-11-13 22:17:00','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('38','2','4','2025-11-13','2025-11-14','1',NULL,'booked','ORD_25435801','TEST_49326','4500','TXN_SUCCESS','TEST MODE','0','2025-11-13 22:30:31','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('39','5','15','2025-11-25','2025-11-26','1',NULL,'booked','ORD_54559396','TEST_63520','2200','TXN_TEST','PAYMENTS DISABLED','0','2025-11-25 15:13:14','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('40','5','3','2025-11-25','2025-11-26','1',NULL,'booked','ORD_57603956','TEST_36825','1800','TXN_TEST','PAYMENTS DISABLED','0','2025-11-25 15:23:41','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('41','5','15','2025-11-25','2025-11-26','1',NULL,'booked','ORD_5101076','TEST_72185','2200','TXN_TEST','PAYMENTS DISABLED','0','2025-11-25 15:59:47','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('42','5','3','2025-11-25','2025-11-26','1',NULL,'booked','ORD_53798127','TEST_34469','1800','TXN_TEST','PAYMENTS DISABLED','0','2025-11-25 16:22:50','0','pending',NULL,NULL,NULL,'0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('43','5','3','2025-11-25','2025-11-26','1',NULL,'booked','ORD_52550890',NULL,'0','pending',NULL,'0','2025-11-25 16:43:40','0','paid',NULL,NULL,NULL,'0.00','2025-11-25 16:57:20','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('44','5','3','2025-11-25','2025-11-26','0','1','cancelled','ORD_58868917','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-11-25 16:46:29','0','paid','BILLING_5_1764060389_7578.jpg',NULL,NULL,'0.00','2025-11-25 16:58:13','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('45','5','4','2025-11-25','2025-11-26','1',NULL,'booked','ORD_5698869','OFFLINE','4500','AWAITING_PROOF','Awaiting manual verification','0','2025-11-25 17:00:59','0','paid','BILLING_5_1764061259_6725.jpg',NULL,NULL,'0.00','2025-11-25 22:11:33','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('46','5','4','2025-11-25','2025-11-26','1',NULL,'booked','ORD_53617856','OFFLINE','4500','AWAITING_PROOF','Awaiting manual verification','1','2025-11-25 22:11:12','0','paid','BILLING_5_1764079872_5821.jpg',NULL,NULL,'0.00','2025-11-26 15:48:46','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('47','2','4','2025-11-26','2025-11-27','1',NULL,'booked','ORD_28952201','OFFLINE','4500','AWAITING_PROOF','Awaiting manual verification','0','2025-11-26 15:48:20','0','paid','BILLING_2_1764143300_1085.jpg',NULL,NULL,'0.00','2025-11-26 15:48:52','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('48','2','3','2025-11-26','2025-11-27','0','1','cancelled','ORD_2225254','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-11-26 15:49:48','0','paid','BILLING_2_1764143388_4914.jpg',NULL,NULL,'0.00','2025-11-26 15:52:13','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('49','2','3','2025-11-26','2025-11-27','0','1','cancelled','ORD_27987813','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-11-26 16:06:31','0','paid','BILLING_2_1764144391_5332.jpg',NULL,NULL,'0.00','2025-11-26 16:06:48','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('50','2','3','2025-11-26','2025-11-27','0','1','cancelled','ORD_23808575','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-11-26 16:12:39','0','paid','BILLING_2_1764144759_8099.jpg',NULL,NULL,'0.00','2025-11-26 16:13:52','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('51','2','3','2025-11-26','2025-11-27','1',NULL,'booked','ORD_22331935','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2025-11-26 16:15:21','0','paid','BILLING_2_1764144921_3341.jpg',NULL,NULL,'0.00','2025-11-26 16:16:12','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('52','2','3','2025-11-26','2025-11-27','1',NULL,'booked','ORD_27916330','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2025-11-26 16:16:03','0','paid','BILLING_2_1764144963_7909.jpg',NULL,NULL,'0.00','2025-11-26 16:16:15','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('53','2','3','2025-11-26','2025-11-27','0','1','cancelled','ORD_21806527','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-11-26 16:17:23','0','paid','BILLING_2_1764145043_2702.jpg',NULL,NULL,'0.00','2025-11-26 16:17:30','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('54','2','15','2025-12-01','2025-12-02','0','1','cancelled','ORD_28453045','OFFLINE','2200','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-12-01 13:45:17','0','paid','BILLING_2_1764567917_8090.jpg',NULL,NULL,'0.00','2025-12-01 13:45:43','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('55','2','4','2025-12-01','2025-12-02','0','1','cancelled','ORD_22390904','OFFLINE','4500','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-12-01 17:05:47','0','paid','BILLING_2_1764579947_3245.jpg','uploads/refund_proofs/refund_55_1774946972.jpg','2250.00','0.00','2025-12-01 17:06:26','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('56','2','15','2025-12-01','2025-12-02','1',NULL,'booked','ORD_26816936','OFFLINE','2200','AWAITING_PROOF','Awaiting manual verification','0','2025-12-01 18:37:48','0','paid','BILLING_2_1764585468_9138.jpg',NULL,NULL,'0.00','2025-12-01 18:38:25','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('57','2','3','2025-12-01','2025-12-02','0','1','cancelled','ORD_26698121','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2025-12-01 18:44:50','0','paid','BILLING_2_1764585890_7226.jpg','uploads/refund_proofs/refund_57_1775115316.jpg','900.00','0.00','2025-12-01 18:44:58','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('58','5','15','2026-02-05','2026-02-06','1',NULL,'booked','ORD_5117609','OFFLINE','2200','AWAITING_PROOF','Awaiting manual verification','0','2026-02-05 19:58:46','0','paid','BILLING_5_1770292726_1789.jpg',NULL,NULL,'0.00','2026-02-05 19:59:06','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('59','5','15','2026-02-18','2026-02-24','1',NULL,'booked','ORD_5218670','OFFLINE','13200','AWAITING_PROOF','Awaiting manual verification','1','2026-02-18 16:14:49','0','paid','BILLING_5_1771402489_4943.jpg',NULL,NULL,'0.00','2026-02-18 16:15:26','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('60','5','15','2026-02-18','2026-02-19','0','1','cancelled','ORD_59799058','OFFLINE','2200','AWAITING_PROOF','Awaiting manual verification',NULL,'2026-02-18 16:18:04','0','paid','BILLING_5_1771402684_4358.jpg','uploads/refund_proofs/refund_60_1775115327.jpg','1100.00','0.00','2026-02-18 16:19:42','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('61','2','3','2026-03-12','2026-03-13','1',NULL,'booked','ORD_2837084','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2026-03-12 16:10:02','0','paid','BILLING_2_1773303002_4089.png',NULL,NULL,'0.00','2026-03-12 16:16:59','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('62','2','4','2026-03-12','2026-03-13','1',NULL,'booked','ORD_29825595','OFFLINE','4500','AWAITING_PROOF','Awaiting manual verification','0','2026-03-12 16:14:35','0','paid','BILLING_2_1773303275_5083.jpg',NULL,NULL,'0.00','2026-03-12 16:14:59','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('63','2','3','2026-03-12','2026-03-13','1',NULL,'booked','ORD_23837250','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2026-03-12 16:37:58','0','paid','BILLING_2_1773304678_4044.jpg',NULL,NULL,'0.00','2026-03-12 16:45:09','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('64','2','3','2026-03-16','2026-03-17','1',NULL,'booked','ORD_22741997','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2026-03-16 17:57:29','0','paid','BILLING_2_1773655049_6432.jpg',NULL,NULL,'0.00','2026-03-16 17:58:03','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('65','2','3','2026-03-16','2026-03-17','0','1','cancelled','ORD_23442853','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification',NULL,'2026-03-16 17:59:13','0','paid','BILLING_2_1773655153_2109.png','uploads/refund_proofs/refund_65_1775115337.jpg','900.00','0.00','2026-03-16 17:59:54','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('66','2','3','2026-03-19','2026-03-20','1',NULL,'booked','ORD_26816858','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2026-03-19 21:19:04','0','paid','BILLING_2_1773926344_4692.jpg',NULL,NULL,'0.00','2026-03-19 21:19:26','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('67','2','3','2026-03-19','2026-03-20','1',NULL,'booked','ORD_21219048','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2026-03-19 21:34:14','0','paid','BILLING_2_1773927254_7986.png',NULL,NULL,'0.00','2026-03-19 22:19:28','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('68','2','3','2026-03-19','2026-03-20','1',NULL,'booked','ORD_28085878','OFFLINE','1800','AWAITING_PROOF','Awaiting manual verification','0','2026-03-19 22:19:02','0','paid','BILLING_2_1773929942_1636.jpg',NULL,NULL,'0.00','2026-03-19 22:19:20','0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('69','9','3','2026-03-31','2026-04-01','1',NULL,'booked','ORD_94838710','OFFLINE','1200','AWAITING_PROOF','Awaiting manual verification','0','2026-03-31 16:41:03','0','paid','BILLING_9_1774946463_5454.jpg',NULL,NULL,'1200.00','2026-03-31 16:47:16','2400.00','1200.00','1200.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('70','9','3','2026-03-31','2026-04-01','1',NULL,'booked','ORD_95631391','OFFLINE','2075','AWAITING_PROOF','Awaiting manual verification','0','2026-03-31 16:46:00','0','paid','BILLING_9_1774946760_9218.jpg',NULL,NULL,'2075.00','2026-03-31 16:47:24','4150.00','2075.00','2075.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('71','9','3','2026-03-31','2026-04-04','0','1','cancelled','ORD_99431650','OFFLINE','4500','AWAITING_PROOF','Awaiting manual verification',NULL,'2026-03-31 16:48:22','0','paid','BILLING_9_1774946902_4645.jpg','uploads/refund_proofs/refund_71_1774947001.jpg','2250.00','4500.00','2026-03-31 16:49:06','9000.00','4500.00','4500.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('72','2','3','2026-04-01','2026-04-03','0',NULL,'booked','ORD_29336440','OFFLINE','2100','AWAITING_PROOF','Awaiting manual verification',NULL,'2026-03-31 22:50:16','0','paid','BILLING_2_1774968616_5445.jpg',NULL,NULL,'2100.00','2026-03-31 22:52:04','4200.00','2100.00','2100.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('900001','2','3','2022-07-20','2022-07-21','1','1','cancelled','TEST_CANCEL_900001',NULL,'0','pending',NULL,'0','2022-07-20 01:50:12','1','pending',NULL,'uploads/refund_proofs/refund_900001_1775115348.jpg','0.00','0.00',NULL,'0.00','0.00','0.00',NULL,'0.00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`is_archived`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) VALUES ('900002','2','3','2026-04-02','2026-04-03','0',NULL,'booked','ORD_23158966','OFFLINE','1088','AWAITING_PROOF','Awaiting manual verification',NULL,'2026-04-02 15:25:29','0','paid','BILLING_2_1775114729_1409.png',NULL,NULL,'1088.00','2026-04-02 15:36:10','2175.00','1088.00','1087.00',NULL,'0.00');

DROP TABLE IF EXISTS `carousel`;
CREATE TABLE `carousel` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(150) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `carousel` (`sr_no`,`image`) VALUES ('39','IMG_70976.png');
INSERT INTO `carousel` (`sr_no`,`image`) VALUES ('45','IMG_49642.png');
INSERT INTO `carousel` (`sr_no`,`image`) VALUES ('46','IMG_54268.png');
INSERT INTO `carousel` (`sr_no`,`image`) VALUES ('47','IMG_88557.png');

DROP TABLE IF EXISTS `contact_details`;
CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(50) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `pn2` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `insta` varchar(100) NOT NULL,
  `tw` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contact_details` (`sr_no`,`address`,`gmap`,`pn1`,`pn2`,`email`,`fb`,`insta`,`tw`,`iframe`) VALUES ('1','Sto. Nino, San Felipe, Zambales','https://goo.gl/maps/T1YM8d4fJsoczstd6','9075767425','9916057372','kellara0227@gmail.com','https://www.facebook.com/','https://www.facebook.com/','https://www.facebook.com/','https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1926.3610332391586!2d120.0585267!3d15.0634815!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3395d53e5d9c2265:0xf64dd77aa6a79bb!2sTraveller’s Place!5e0!3m2!1sen!2sph!4v1741351613979!5m2!1sen!2sph');

DROP TABLE IF EXISTS `email_logs`;
CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `related_booking_id` int(11) DEFAULT NULL,
  `related_user_id` int(11) DEFAULT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(150) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `template_key` varchar(80) NOT NULL DEFAULT 'general',
  `status` enum('queued','sent','failed') NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `triggered_by` varchar(60) DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `related_booking_id` (`related_booking_id`),
  KEY `related_user_id` (`related_user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('1',NULL,NULL,'Kellara0227@gmail.com','Keith Eimreh Lara','Booking Received – Travelers Place','booking_received','sent','','admin_panel','2026-03-31 22:50:23');
INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('2',NULL,NULL,'Kellara0227@gmail.com','Keith Eimreh Lara','Booking Confirmed ORD_29336440 – Travelers Place','booking_confirmed','sent','','admin_panel','2026-03-31 22:52:09');
INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('3',NULL,NULL,'Kellara0227@gmail.com','Keith Eimreh Lara','Booking Received – Travelers Place','booking_received','sent','','admin_panel','2026-04-02 15:25:34');
INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('4',NULL,NULL,'codexqa1775143939@gmail.com','','Verify Your Email – Travelers Place','verification','sent','','guest_flow','2026-04-02 15:32:24');
INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('5',NULL,NULL,'Kellara0227@gmail.com','Keith Eimreh Lara','Booking Confirmed ORD_23158966 – Travelers Place','booking_confirmed','sent','','admin_panel','2026-04-02 15:36:16');
INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('6',NULL,NULL,'codexblock1775144290@gmail.com','','Verify Your Email – Travelers Place','verification','sent','','guest_flow','2026-04-02 15:38:14');
INSERT INTO `email_logs` (`id`,`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`,`created_at`) VALUES ('7',NULL,NULL,'codexblock1775144290@gmail.com','','Password Reset – Travelers Place','password_reset','sent','','guest_flow','2026-04-02 15:38:36');

DROP TABLE IF EXISTS `extras`;
CREATE TABLE `extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `extras` (`id`,`name`,`price`,`description`,`status`) VALUES ('1','Extra Matress','300.00','','1');
INSERT INTO `extras` (`id`,`name`,`price`,`description`,`status`) VALUES ('2','Extra Pillow','50.00','','1');
INSERT INTO `extras` (`id`,`name`,`price`,`description`,`status`) VALUES ('3','Extra Fan','150.00','','1');
INSERT INTO `extras` (`id`,`name`,`price`,`description`,`status`) VALUES ('4','Extra Blanket','75.00','','1');

DROP TABLE IF EXISTS `facilities`;
CREATE TABLE `facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('25','IMG_25011.svg','WiFi','Stay Connected, Always\r\n\r\nEnjoy complimentary high-speed WiFi throughout your stay, whether you&#039;re working, streaming, or staying in touch with loved ones.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('26','IMG_43951.svg','Air Conditioning','Personalized Comfort in Every Room\r\n\r\nStay cool and comfortable with our individually controlled air conditioning system, designed to provide the perfect temperature for your stay.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('27','IMG_23959.svg','Karaoke','Sing Your Heart Out in Style!\r\n\r\nUnleash your inner superstar in our premium karaoke rooms, perfect for parties, celebrations, or a fun night out with friends and family.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('28','IMG_67215.svg','Television','Your Personal Home Theater\r\n\r\nEnjoy your favorite shows, movies, and more with our high-definition LED Smart TVs in every room.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('29','IMG_52316.svg','Microwave','Quick &amp; Convenient Meals\r\n\r\nEnjoy the convenience of an in-room microwave for heating up snacks, leftovers, or quick meals during your stay.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('30','IMG_17789.svg','Mini-refrigerator','Keep Your Refreshments Chilled\r\n\r\nEnjoy the convenience of an in-room mini refrigerator to store drinks, snacks, and leftovers during your stay.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('31','IMG_82022.svg','Water Boiler','Hot Drips, Anytime\r\n\r\nEnjoy the convenience of an in-room electric kettle for making tea, coffee, or instant meals at your leisure.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('32','IMG_33278.svg','Electric Fan','Stay Cool &amp; Comfortable\r\n\r\nEnjoy a breezy, comfortable stay with our in-room electric fan, perfect for extra air circulation or as a cooling alternative.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('33','IMG_29079.svg','Hair Dryer','Styling Made Easy\r\n\r\nLook your best with our professional-grade hair dryer, available in every room for your convenience.');

DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `features` (`id`,`name`) VALUES ('15','kitchen');
INSERT INTO `features` (`id`,`name`) VALUES ('17','sofa');
INSERT INTO `features` (`id`,`name`) VALUES ('22','Bathroom');
INSERT INTO `features` (`id`,`name`) VALUES ('23','Breakfast');
INSERT INTO `features` (`id`,`name`) VALUES ('25','massage');

DROP TABLE IF EXISTS `guest_notes`;
CREATE TABLE `guest_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `note_type` varchar(30) NOT NULL DEFAULT 'internal',
  `title` varchar(150) NOT NULL,
  `note` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `guest_notes` (`id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`) VALUES ('1','2',NULL,'info','YEssir','hi ya','3','2026-03-31 21:39:45','2026-03-31 21:39:45');
INSERT INTO `guest_notes` (`id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`) VALUES ('2','2',NULL,'info','YEssir','hi ya','3','2026-03-31 21:40:32','2026-03-31 21:40:32');

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('booking','payment','refund','system') NOT NULL DEFAULT 'system',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  KEY `type` (`type`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('1','5','42','Your booking #42 has been confirmed!','0','2025-11-25 16:22:57','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('2','5','43','Booking #43 confirmed on Nov 25, 2025 4:57 PM for Couple Room (Room 2). Stay: Nov 25, 2025 to Nov 26, 2025.','0','2025-11-25 16:57:20','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('3','5','44','Booking #44 confirmed on Nov 25, 2025 4:58 PM for Couple Room (Room 2). Stay: Nov 25, 2025 to Nov 26, 2025.','0','2025-11-25 16:58:13','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('4','5','45','Booking #45 confirmed on Nov 25, 2025 10:11 PM for Double-Decker Deluxe (Room 1). Stay: Nov 25, 2025 to Nov 26, 2025.','0','2025-11-25 22:11:33','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('5','5','46','Booking #46 confirmed on Nov 26, 2025 3:48 PM for Double-Decker Deluxe (Room 3). Stay: Nov 25, 2025 to Nov 26, 2025.','0','2025-11-26 15:48:46','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('6','2','47','Booking #47 confirmed on Nov 26, 2025 3:48 PM for Double-Decker Deluxe (Room 6). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 15:48:52','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('7','2','48','Booking #48 confirmed on Nov 26, 2025 3:52 PM for Couple Room (Room 1). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 15:52:13','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('8','2','49','Booking #49 confirmed on Nov 26, 2025 4:06 PM for Couple Room (Room 1). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 16:06:48','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('9','2','50','Booking #50 confirmed on Nov 26, 2025 4:13 PM for Couple Room (Room 4). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 16:13:52','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('10','2','51','Booking #51 confirmed on Nov 26, 2025 4:16 PM for Couple Room (Room 1). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 16:16:12','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('11','2','52','Booking #52 confirmed on Nov 26, 2025 4:16 PM for Couple Room (Room 4). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 16:16:15','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('12','2','53','Booking #53 confirmed on Nov 26, 2025 4:17 PM for Couple Room (Room 2). Stay: Nov 26, 2025 to Nov 27, 2025.','1','2025-11-26 16:17:30','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('13','2','54','Booking #54 confirmed on Dec 01, 2025 1:45 PM for Family Room (Room 4). Stay: Dec 01, 2025 to Dec 02, 2025.','1','2025-12-01 13:45:43','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('14','2','55','Booking #55 confirmed on Dec 01, 2025 5:06 PM for Double-Decker Deluxe (Room 3). Stay: Dec 01, 2025 to Dec 02, 2025.','1','2025-12-01 17:06:26','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('15','2','56','Booking #56 confirmed on Dec 01, 2025 6:38 PM for Family Room (Room 1). Stay: Dec 01, 2025 to Dec 02, 2025.','1','2025-12-01 18:38:25','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('16','2','57','Booking #57 confirmed on Dec 01, 2025 6:44 PM for Couple Room (Room 1). Stay: Dec 01, 2025 to Dec 02, 2025.','1','2025-12-01 18:44:58','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('17','5','58','Booking #58 confirmed on Feb 05, 2026 7:59 PM for Family Room (Room 2). Stay: Feb 05, 2026 to Feb 06, 2026.','0','2026-02-05 19:59:06','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('18','5','59','Booking #59 confirmed on Feb 18, 2026 4:15 PM for Family Room (Room 1). Stay: Feb 18, 2026 to Feb 24, 2026.','0','2026-02-18 16:15:26','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('19','5','60','Booking #60 confirmed on Feb 18, 2026 4:19 PM for Family Room (Room 5). Stay: Feb 18, 2026 to Feb 19, 2026.','0','2026-02-18 16:19:42','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('20','2','62','Booking #62 confirmed on Mar 12, 2026 4:14 PM for Double-Decker Deluxe (Room 4). Stay: Mar 12, 2026 to Mar 13, 2026.','1','2026-03-12 16:14:59','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('21','2','61','Booking #61 confirmed on Mar 12, 2026 4:16 PM for Couple Room (Room 1). Stay: Mar 12, 2026 to Mar 13, 2026.','1','2026-03-12 16:16:59','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('22','2','63','Booking #63 confirmed on Mar 12, 2026 4:45 PM for Couple Room (Room 4). Stay: Mar 12, 2026 to Mar 13, 2026.','1','2026-03-12 16:45:09','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('23','2','64','Booking #64 confirmed on Mar 16, 2026 5:58 PM for Couple Room (Room 1). Stay: Mar 16, 2026 to Mar 17, 2026.','1','2026-03-16 17:58:03','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('24','2','65','Booking #65 confirmed on Mar 16, 2026 5:59 PM for Couple Room (Room 5). Stay: Mar 16, 2026 to Mar 17, 2026.','1','2026-03-16 17:59:54','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('25','2','66','Booking #66 confirmed on Mar 19, 2026 9:19 PM for Couple Room (Room 3). Stay: Mar 19, 2026 to Mar 20, 2026.','1','2026-03-19 21:19:26','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('26','2','68','Booking #68 confirmed on Mar 19, 2026 10:19 PM for Couple Room (Room 10). Stay: Mar 19, 2026 to Mar 20, 2026.','1','2026-03-19 22:19:20','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('27','2','67','Booking #67 confirmed on Mar 19, 2026 10:19 PM for Couple Room (Room 6). Stay: Mar 19, 2026 to Mar 20, 2026.','1','2026-03-19 22:19:28','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('28','9','69','Booking #69 confirmed on Mar 31, 2026 4:47 PM for Couple Room (Room 3). Stay: Mar 31, 2026 to Apr 01, 2026.','0','2026-03-31 16:47:16','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('29','9','70','Booking #70 confirmed on Mar 31, 2026 4:47 PM for Couple Room (Room 3). Stay: Mar 31, 2026 to Apr 01, 2026.','0','2026-03-31 16:47:24','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('30','9','71','Booking #71 confirmed on Mar 31, 2026 4:49 PM for Couple Room (Room 7). Stay: Mar 31, 2026 to Apr 04, 2026.','0','2026-03-31 16:49:06','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('31','2','55','Your refund of ₱2,250.00 for booking #55 has been processed. Proof of refund has been uploaded — you can view it in your notifications.','1','2026-03-31 16:49:32','refund');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('32','9','71','Your refund of ₱2,250.00 for booking #71 has been processed. Proof of refund has been uploaded — you can view it in your notifications.','1','2026-03-31 16:50:01','refund');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('33','2','68','Customer service updated your ticket: mag book sana kol','1','2026-03-31 21:41:27','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('34','2','72','Booking #72 confirmed on Mar 31, 2026 10:52 PM for Couple Room (Room 4). Stay: Apr 01, 2026 to Apr 03, 2026.','1','2026-03-31 22:52:04','system');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('35','2','57','Your refund of ₱900.00 for booking #57 has been processed. Proof of refund has been uploaded — you can view it in your notifications.','1','2026-04-02 15:35:16','refund');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('36','5','60','Your refund of ₱1,100.00 for booking #60 has been processed. Proof of refund has been uploaded — you can view it in your notifications.','0','2026-04-02 15:35:27','refund');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('37','2','65','Your refund of ₱900.00 for booking #65 has been processed. Proof of refund has been uploaded — you can view it in your notifications.','1','2026-04-02 15:35:37','refund');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('38','2','900001','Your refund of ₱0.00 for booking #900001 has been processed. Proof of refund has been uploaded — you can view it in your notifications.','1','2026-04-02 15:35:48','refund');
INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`) VALUES ('39','2','900002','Booking #900002 confirmed on Apr 02, 2026 3:36 PM for Couple Room (Room 7). Stay: Apr 02, 2026 to Apr 03, 2026.','1','2026-04-02 15:36:10','system');

DROP TABLE IF EXISTS `promo_codes`;
CREATE TABLE `promo_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` varchar(20) NOT NULL DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `usage_limit` int(11) NOT NULL DEFAULT 0,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `promo_codes` (`id`,`code`,`description`,`discount_type`,`discount_value`,`min_amount`,`max_discount`,`start_date`,`end_date`,`usage_limit`,`used_count`,`is_active`,`created_by`,`created_at`) VALUES ('1','FOOLSMONTH','April Fool\'s Promo','fixed','1.00','50.00','100.00','2026-04-01','2026-04-30','1','0','1','3','2026-03-31 22:25:30');

DROP TABLE IF EXISTS `promo_redemptions`;
CREATE TABLE `promo_redemptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `promo_id` (`promo_id`),
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `rating_review`;
CREATE TABLE `rating_review` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` varchar(200) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sr_no`),
  KEY `booking_id` (`booking_id`),
  KEY `room_id` (`room_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `rating_review_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`),
  CONSTRAINT `rating_review_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `rating_review_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('5','13','4','2','3','2asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:30');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('6','12','3','2','1','3asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:34');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('8','14','5','2','5','1asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:25');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('9','12','3','2','1','3asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:34');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('10','12','3','2','1','3asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:34');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('12','21','4','5','5','lamig pag mag isa jan boi','1','2025-09-09 19:10:20');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('15','46','4','5','5','qwerty','1','2025-12-01 18:58:36');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('16','59','15','5','5','qwewqe','1','2026-02-18 16:23:14');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('17','59','15','5','5','qwewqe','1','2026-02-18 16:23:15');

DROP TABLE IF EXISTS `room_block_dates`;
CREATE TABLE `room_block_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `room_no` varchar(20) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `block_type` varchar(30) NOT NULL DEFAULT 'maintenance',
  `reason` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_block_dates` (`id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`) VALUES ('1','3','1','2026-03-31','2026-04-02','event','party party','cancelled','3','2026-03-31 21:30:49');
INSERT INTO `room_block_dates` (`id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`) VALUES ('2','4','2b','2026-04-01','2026-04-02','maintenance','qw','cancelled','3','2026-03-31 21:31:42');
INSERT INTO `room_block_dates` (`id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`) VALUES ('3','15','1','2026-04-01','2026-04-02','unavailable','u','cancelled','3','2026-03-31 21:38:05');
INSERT INTO `room_block_dates` (`id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`) VALUES ('4','15','2','2026-04-01','2026-04-02','maintenance','q','cancelled','3','2026-03-31 21:38:33');
INSERT INTO `room_block_dates` (`id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`) VALUES ('5','15','2','2026-04-01','2026-04-02','maintenance','q','cancelled','3','2026-03-31 21:38:49');

DROP TABLE IF EXISTS `room_facilities`;
CREATE TABLE `room_facilities` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL,
  PRIMARY KEY (`sr_no`),
  KEY `facilities id` (`facilities_id`),
  KEY `room id` (`room_id`),
  CONSTRAINT `facilities id` FOREIGN KEY (`facilities_id`) REFERENCES `facilities` (`id`) ON UPDATE NO ACTION,
  CONSTRAINT `room id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('158','4','25');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('159','4','26');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('160','4','27');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('161','4','28');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('162','4','29');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('163','4','30');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('164','4','31');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('165','4','32');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('166','4','33');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('167','15','25');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('168','15','26');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('169','15','27');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('170','15','28');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('171','15','29');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('172','15','30');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('173','15','31');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('174','15','32');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('175','15','33');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('209','3','25');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('210','3','26');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('211','3','27');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('212','3','28');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('213','3','29');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('214','3','30');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('215','3','31');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('216','3','32');

DROP TABLE IF EXISTS `room_features`;
CREATE TABLE `room_features` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `features_id` int(11) NOT NULL,
  PRIMARY KEY (`sr_no`),
  KEY `features id` (`features_id`),
  KEY `rm id` (`room_id`),
  CONSTRAINT `features id` FOREIGN KEY (`features_id`) REFERENCES `features` (`id`) ON UPDATE NO ACTION,
  CONSTRAINT `rm id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('129','4','15');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('130','4','17');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('131','4','22');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('132','4','23');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('133','15','15');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('134','15','17');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('135','15','22');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('136','15','23');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('153','3','15');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('154','3','17');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('155','3','22');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('156','3','23');

DROP TABLE IF EXISTS `room_images`;
CREATE TABLE `room_images` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sr_no`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('29','1','single.jpg\r\n','0');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('31','4','IMG_48037.jpg','1');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('32','3','IMG_78227.jpg','1');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('34','15','IMG_49836.jpg','1');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('35','16','IMG_62603.jpg','1');

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` varchar(350) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `removed` int(11) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('1','simple room','159','58','56','12','2','asdf asd','1','1',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('2','simple room 2','785','159','85','452','10','adfasdfa sd','1','1',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('3','Couple Room','220','1800','10','2','1','Name: Couple Room\r\nDescription: A cozy and romantic room perfect for couples, featuring a comfortable queen-sized bed, air conditioning, and a private bathroom. Ideal for a relaxing getaway.\r\nArea: 24 sq.m. (250 sq.ft.)\r\nBeds: 1 Queen Size Bed\r\nMax Guests: 2 Adults\r\nChildren: 1 Child (under 12)\r\nPrice: ₱1,800 per night','1','0',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('4','Double-Decker Deluxe','300','4500','6','4','8','Name: Double-Deck Deluxe\r\nDescription: Our most spacious room featuring two double-deck double-sized beds. Ideal for groups or large families. Includes air conditioning, a private bathroom, and a work desk. Perfect for long stays.\r\nArea: 42 sq.m. (450 sq.ft.)\r\nBeds: 2 Double-Deck Double Beds (4 beds total)\r\nMax Guests: 6 Adults\r\nChildren: 3 Childre','1','0',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('5','Family Room','600','3600','7','2','2','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('6','Supreme deluxe room','500','900','12','9','10','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','1',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('7','asc','23','2','1','1','1','f','1','1',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('8','ASD','123','234','2','2','22','SDF','1','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('9','asd','12','123','2','123','2','123d','1','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('10','sad','2','12','2','21','21','12','0','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('11','ffgg','22','12','1','2','12','sdf','0','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('12','sdddd','12','12','1','123','123','sdfa','0','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('13','dv','2','12','2','123','123','dfvds','0','1','2025-10-30 21:50:39','1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('14','dwf','1','12','1','12','2','sdf','0','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('15','Family Room','250','2200','7','2','2','Spacious room designed for families, featuring one queen-sized bed and one double bed. Includes air conditioning, a private bathroom, and a sitting area. Perfect for small families or groups.','1','0',NULL,'0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('16','qwe','1','1','15','22','2','12sd','0','1',NULL,'1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`archived_at`,`is_archived`) VALUES ('17','xc','12','12','121','1','2','SDF','0','1',NULL,'1');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(50) NOT NULL,
  `site_about` varchar(250) NOT NULL,
  `shutdown` tinyint(1) NOT NULL,
  `payment_gcash_number` varchar(100) DEFAULT NULL,
  `payment_maya_number` varchar(100) DEFAULT NULL,
  `payment_gcash_qr` varchar(255) DEFAULT NULL,
  `payment_maya_qr` varchar(255) DEFAULT NULL,
  `booking_rules` text DEFAULT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`sr_no`,`site_title`,`site_about`,`shutdown`,`payment_gcash_number`,`payment_maya_number`,`payment_gcash_qr`,`payment_maya_qr`,`booking_rules`) VALUES ('1','Travelers Place','Welcome to Travelers Place, your cozy retreat in the heart of Santo Niño, San Felipe, Zambales. We’re more than just a place to stay—we’re your home away from home. With clean rooms, fun amenities, and warm hospitality, we make sure every guest feels','0','09916057372','09075767425','http://127.0.0.1/SE/images/payments/PAYMENT_GCASH_QR_1764570103629.jpg','http://127.0.0.1/SE/images/payments/PAYMENT_MAYA_QR_1764570103219.jpg','50% downpayment is required to confirm your booking.\r\nThe remaining 50% balance is due upon check-in.\r\nCheck-in time is 2:00 PM; Check-out time is 12:00 PM (noon).\r\nCancellations will be refunded 50% of the total amount paid.\r\nGuests are responsible for any damage to room property during their stay.\r\nNo smoking inside the rooms. Designated smoking areas are available outside.\r\nPlease observe quiet hours from 10:00 PM to 7:00 AM.\r\nA valid government-issued ID is required upon check-in.');

DROP TABLE IF EXISTS `support_canned_replies`;
CREATE TABLE `support_canned_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `category` varchar(40) NOT NULL DEFAULT 'general',
  `reply_text` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `support_canned_replies` (`id`,`title`,`category`,`reply_text`,`is_active`,`created_by`,`created_at`) VALUES ('1','Booking received','booking','We have received your booking request and our team is currently reviewing the submitted details. We will update you as soon as verification is completed.','1',NULL,'2026-03-31 20:26:59');
INSERT INTO `support_canned_replies` (`id`,`title`,`category`,`reply_text`,`is_active`,`created_by`,`created_at`) VALUES ('2','Refund in progress','refund','Your refund request is already in process. Once the transfer is completed, we will upload the proof and send you a confirmation update.','1',NULL,'2026-03-31 20:26:59');
INSERT INTO `support_canned_replies` (`id`,`title`,`category`,`reply_text`,`is_active`,`created_by`,`created_at`) VALUES ('3','Need more payment details','payment','Thank you for your payment submission. We need a clearer screenshot or reference number to verify it properly. Please reply here and attach the updated proof.','1',NULL,'2026-03-31 20:26:59');

DROP TABLE IF EXISTS `support_ticket_messages`;
CREATE TABLE `support_ticket_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_type` varchar(20) NOT NULL DEFAULT 'guest',
  `sender_id` int(11) DEFAULT NULL,
  `sender_name` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  `seen_by_user` tinyint(1) NOT NULL DEFAULT 0,
  `seen_by_staff` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `seen_by_user` (`seen_by_user`),
  KEY `seen_by_staff` (`seen_by_staff`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `support_ticket_messages` (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`) VALUES ('1','1','guest','2','Keith Eimreh Lara','qwewe',NULL,'0','1','1','2026-03-31 21:40:21');
INSERT INTO `support_ticket_messages` (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`) VALUES ('2','1','admin','3','Admin','Thank you for your payment submission. We need a clearer screenshot or reference number to verify it properly. Please reply here and attach the updated proof.','uploads/ticket_attachments/ticket_1774964487_1419.jpg','0','1','1','2026-03-31 21:41:27');
INSERT INTO `support_ticket_messages` (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`) VALUES ('3','1','guest','2','Keith Eimreh Lara','olats sayo oma','uploads/ticket_attachments/ticket_1774968238_9632.png','0','1','1','2026-03-31 22:43:58');
INSERT INTO `support_ticket_messages` (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`) VALUES ('4','1','guest','2','Keith Eimreh Lara','olats sayo oma','uploads/ticket_attachments/ticket_1774968491_3858.png','0','1','1','2026-03-31 22:48:12');

DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `order_id` varchar(80) DEFAULT NULL,
  `subject` varchar(180) NOT NULL,
  `category` varchar(40) NOT NULL DEFAULT 'general',
  `priority` varchar(20) NOT NULL DEFAULT 'normal',
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `assigned_to` int(11) DEFAULT NULL,
  `escalated` tinyint(1) NOT NULL DEFAULT 0,
  `last_reply_at` datetime DEFAULT NULL,
  `last_reply_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_code` (`ticket_code`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `support_tickets` (`id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`) VALUES ('1','TIC-20260331-1398','2','68','ORD_28085878','mag book sana kol','general','urgent','resolved',NULL,'1','2026-03-31 22:48:12','guest','2026-03-31 21:40:21','2026-04-02 15:50:06');

DROP TABLE IF EXISTS `team_details`;
CREATE TABLE `team_details` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `picture` varchar(150) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `team_details` (`sr_no`,`name`,`picture`) VALUES ('13','Lebron Tagalog','IMG_28805.jpg');

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) NOT NULL,
  `room_no` varchar(50) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('1','2','Kobesakol','a2','600','online','paid','payment','0','2022-07-20 02:14:44');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('2','3','Lebron Tagalog',NULL,'1800','online','paid','payment','0','2022-07-20 02:19:00');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('3','4','Keith',NULL,'4500','online','failed','payment','0','2022-07-20 02:30:52');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('4','5','Keith',NULL,'900','online','paid','payment','0','2022-07-20 02:32:09');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('5','6','Keith','52','7200','online','paid','payment','0','2022-07-20 02:34:46');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('6','8','Keith',NULL,'600','online','paid','payment','0','2022-08-14 01:16:05');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('7','9','Keith Lara\r\n','159A','3000','online','paid','payment','0','2022-08-15 19:32:05');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('8','10','Keith','15S','1800','online','paid','payment','0','2022-08-15 19:32:58');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('9','11','Keith','1','2700','online','paid','payment','0','2022-08-20 00:19:57');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('10','12','Keith','2','1200','online','paid','payment','0','2022-08-20 00:20:23');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('11','13','Keith','23','3000','online','paid','payment','0','2022-08-20 00:20:45');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('12','14','Keith','44','2400','online','paid','payment','0','2022-08-20 00:21:06');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('13','15','Keith',NULL,'1200','online','paid','payment','0','2019-08-21 01:51:28');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('14','16','Keith','1','1200','online','paid','payment','0','2022-08-25 01:52:04');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('15','17','Keith','20A','900','online','paid','payment','0','2022-09-08 01:15:30');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('16','21','keiths','1','1800','online','paid','payment','0','2025-09-09 19:06:59');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('17','22','keiths',NULL,'4800','online','paid','payment','0','2025-09-09 19:17:45');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('18','23','keiths','1','36000','online','paid','payment','0','2025-09-09 19:25:11');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('19','24','keiths',NULL,'14400','online','paid','payment','0','2025-09-15 18:01:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('20','25','keiths','2A','3600','online','paid','payment','0','2025-09-15 20:09:59');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('21','26','keiths',NULL,'1800','online','paid','payment','0','2025-09-15 20:12:06');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('22','27','keiths','3A','3600','online','paid','payment','0','2025-09-15 20:16:17');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('23','28','keiths','2','5000','online','paid','payment','0','2025-10-13 19:18:24');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('24','29','keiths','1','5000','online','paid','payment','0','2025-10-13 20:28:20');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('25','30','keiths','4','1800','online','paid','payment','0','2025-10-30 19:53:10');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('26','31','keiths','1','14400','online','paid','payment','0','2025-11-04 22:36:07');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('27','32','keiths','5','1800','online','paid','payment','0','2025-11-06 02:35:35');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('28','33','keiths','2','1500','online','paid','payment','0','2025-11-06 12:43:08');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('29','34','keiths','3','2200','online','paid','payment','0','2025-11-06 18:06:10');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('30','35','keiths','3','1800','online','paid','payment','0','2025-11-06 20:20:01');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('31','36','keiths','2','1800','online','paid','payment','0','2025-11-13 21:24:11');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('32','37','Keith Eimreh Lara','3','1800','online','paid','payment','0','2025-11-13 22:17:00');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('33','38','Keith Eimreh Lara','5','4500','online','paid','payment','0','2025-11-13 22:30:31');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('34','39','keiths','2','2200','online','failed','payment','0','2025-11-25 15:13:14');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('35','40','keiths','1','1800','online','failed','payment','0','2025-11-25 15:23:41');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('36','41','keiths','1','2200','online','failed','payment','0','2025-11-25 15:59:47');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('37','42','keiths','4','1800','online','failed','payment','0','2025-11-25 16:22:50');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('38','44','keiths','2','1800','online','failed','payment','0','2025-11-25 16:46:29');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('39','45','keiths','1','4500','online','failed','payment','0','2025-11-25 17:00:59');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('40','46','keiths','3','4500','online','failed','payment','0','2025-11-25 22:11:12');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('41','47','Keith Eimreh Lara','6','4500','online','failed','payment','0','2025-11-26 15:48:20');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('42','48','Keith Eimreh Lara','1','1800','online','failed','payment','0','2025-11-26 15:49:48');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('43','49','Keith Eimreh Lara','1','1800','online','failed','payment','0','2025-11-26 16:06:31');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('44','50','Keith Eimreh Lara','4','1800','online','failed','payment','0','2025-11-26 16:12:39');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('45','51','Keith Eimreh Lara','1','1800','online','failed','payment','0','2025-11-26 16:15:21');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('46','52','Keith Eimreh Lara','4','1800','online','failed','payment','0','2025-11-26 16:16:03');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('47','53','Keith Eimreh Lara','2','1800','online','failed','payment','0','2025-11-26 16:17:23');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('48','54','Keith Eimreh Lara','4','2200','online','failed','payment','0','2025-12-01 13:45:17');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('49','55','Keith Eimreh Lara','3','4500','online','failed','payment','0','2025-12-01 17:05:47');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('50','56','Keith Eimreh Lara','1','2200','online','failed','payment','0','2025-12-01 18:37:48');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('51','57','Keith Eimreh Lara','1','1800','online','failed','payment','0','2025-12-01 18:44:50');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('52','58','keiths','2','2200','online','failed','payment','0','2026-02-05 19:58:46');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('53','59','keiths','1','13200','online','failed','payment','0','2026-02-18 16:14:49');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('54','60','keiths','5','2200','online','failed','payment','0','2026-02-18 16:18:04');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('64','3','Lebron Tagalog',NULL,'900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('65','5','Keith',NULL,'450','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('66','7','Keith',NULL,'0','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('67','8','Keith',NULL,'300','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('68','15','Keith',NULL,'600','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('69','22','keiths',NULL,'2400','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('70','23','keiths','1','18000','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('71','24','keiths',NULL,'7200','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('72','26','keiths',NULL,'900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('73','29','keiths','1','2500','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('74','34','keiths','3','1100','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('75','44','keiths','2','900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('76','48','Keith Eimreh Lara','1','900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('77','49','Keith Eimreh Lara','1','900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('78','50','Keith Eimreh Lara','4','900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('79','53','Keith Eimreh Lara','2','900','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('80','54','Keith Eimreh Lara','4','1100','online','refunded','refund','0','2026-03-12 15:50:40');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('81','61','Keith Eimreh Lara','1','1800','online','failed','payment','0','2026-03-12 16:10:02');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('82','62','Keith Eimreh Lara','4','4500','online','failed','payment','0','2026-03-12 16:14:35');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('83','63','Keith Eimreh Lara','4','1800','online','failed','payment','0','2026-03-12 16:37:58');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('84','64','Keith Eimreh Lara','1','1800','online','failed','payment','0','2026-03-16 17:57:29');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('85','65','Keith Eimreh Lara','5','1800','online','failed','payment','0','2026-03-16 17:59:13');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('86','66','Keith Eimreh Lara','3','1800','online','failed','payment','0','2026-03-19 21:19:04');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('87','67','Keith Eimreh Lara','6','1800','online','failed','payment','0','2026-03-19 21:34:14');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('88','68','Keith Eimreh Lara','10','1800','online','failed','payment','0','2026-03-19 22:19:02');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('89','69','Keith Eimreh Lara','3','1200','online','failed','payment','0','2026-03-31 16:41:03');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('90','70','Keith Eimreh Lara','3','2075','online','failed','payment','0','2026-03-31 16:46:00');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('91','71','Keith Eimreh Lara','7','4500','online','failed','payment','0','2026-03-31 16:48:22');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('92','55','Keith Eimreh Lara','3','2250','online','refunded','refund','0','2026-03-31 22:19:48');
INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ('93','71','Keith Eimreh Lara','7','2250','online','refunded','refund','0','2026-03-31 22:19:48');

DROP TABLE IF EXISTS `user_cred`;
CREATE TABLE `user_cred` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `profile` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(200) DEFAULT NULL,
  `t_expire` date DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(100) DEFAULT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_cred_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`is_archived`,`datentime`,`username`,`verification_code`) VALUES ('2','Keith Eimreh Lara','Kellara0227@gmail.com','San Marcelino, Zambales','09916057372','123324','2025-08-10','keiths.jpg','$2y$10$8ETNoOwcCk2pK6GFcHwdKOCoFxpU/IiLfqza2MWPAzkiCElTDnjDy','1','b70f2ed969e85f854b7366d053347312','2026-03-31','1','0','2024-06-12 16:05:59','kellara0227',NULL);
INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`is_archived`,`datentime`,`username`,`verification_code`) VALUES ('5','keiths','kellara@gmail.com','Brgy. La Paz, San Marcelino, Zambales','123454','2207','2025-08-26','IMG_69424.jpg','$2y$10$8ETNoOwcCk2pK6GFcHwdKOCoFxpU/IiLfqza2MWPAzkiCElTDnjDy','1',NULL,NULL,'1','0','2025-08-26 18:45:39','kellara',NULL);
INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`is_archived`,`datentime`,`username`,`verification_code`) VALUES ('6','kobesakol','k...7@gmail.com','Brgy. La Paz, San Marcelino, Zambales','09075767425','2207','2025-08-26','IMG_45290.jpg','$2y$10$jhMbq38EJKbSaFWmvjzv/O/vHFEr8lFyVfgHom9a5kNAjRoDIqRza','1',NULL,NULL,'1','0','2025-08-26 18:47:04','k...7',NULL);
INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`is_archived`,`datentime`,`username`,`verification_code`) VALUES ('7','lebum','lebum@gmail.com','Brgy. La Paz, San Marcelino, Zambales','12313123','0','2026-03-12','IMG_23998.jpg','$2y$10$LmWTOXSUt7hMHF1ASE8PeepBv84M2uhC7LOvir1ehBeQJpjSUxTry','1',NULL,NULL,'1','0','2026-03-12 16:00:28','lebum',NULL);
INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`is_archived`,`datentime`,`username`,`verification_code`) VALUES ('9','Keith Eimreh Lara','keitheimreh1111@gmail.com','Brgy. La Paz, San Marcelino, Zambales','123','0','2026-03-24','IMG_32644.jpg','$2y$10$nqfLySKKQCP8OFb2evXNseRClLqDKUeQwk2rw2K9JobvT1QaaZD6G','1',NULL,NULL,'1','0','2026-03-31 16:38:59','keitheimreh1111',NULL);

DROP TABLE IF EXISTS `user_queries`;
CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`,`is_archived`,`archived_at`) VALUES ('11','Keith Eimreh Lara','kellar@gmail.com','This is one subject','orem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates im','2025-03-11 00:00:00','1','1','2025-03-11 00:00:00');
INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`,`is_archived`,`archived_at`) VALUES ('13','keiths','kellara0227@gmail.com','mag book sana kol','fsadfasdf','2025-09-09 19:44:51','1','0',NULL);
INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`,`is_archived`,`archived_at`) VALUES ('14','Keith Eimreh Lara','kellara0227@gmail.com','english','asdsdfsadf','2025-11-05 23:45:57','1','0',NULL);
INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`,`is_archived`,`archived_at`) VALUES ('15','Keith Eimreh Lara','kellara0227@gmail.com','Booking concern','lami','2025-11-06 18:11:05','1','0',NULL);
INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`,`is_archived`,`archived_at`) VALUES ('16','Keith Eimreh Lara','kellara0227@gmail.com','Booking concern','lami','2025-11-06 18:11:32','1','0',NULL);
INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`,`is_archived`,`archived_at`) VALUES ('17','wedeww2e2s','kellara0227@gmail.com','12312312','1231231231','2026-02-18 16:22:26','1','1','2026-02-18 16:22:26');

SET FOREIGN_KEY_CHECKS=1;

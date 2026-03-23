SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `admin_cred`;
CREATE TABLE `admin_cred` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(150) NOT NULL,
  `admin_pass` varchar(150) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_cred` (`sr_no`,`admin_name`,`admin_pass`) VALUES ('2','keith','123\r\n');
INSERT INTO `admin_cred` (`sr_no`,`admin_name`,`admin_pass`) VALUES ('3','kit','123');

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
  `address` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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
  `archived_at` datetime NOT NULL DEFAULT current_timestamp()
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
  PRIMARY KEY (`sr_no`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('1','1','Simple Room','300','300',NULL,'Keith Eimreh Lara','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('2','2','Simple Room','300','600','a2','Kobesakol','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('3','3','Simple Room','300','1800',NULL,'Lebron Tagalog','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('4','4','Supreme deluxe room','900','4500',NULL,'Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('5','5','Supreme deluxe room','900','900',NULL,'Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('6','6','Supreme deluxe room','900','7200','52','Keith','12323432','ad2342343');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('7','7','Supreme deluxe room','900','900',NULL,'Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('8','8','Simple Room','300','600',NULL,'Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('9','9','Luxury Room','600','3000','159A','Keith Lara\r\n','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('10','10','Luxury Room','600','1800','15S','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('11','11','Supreme deluxe room','900','2700','1','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('12','12','Simple Room','300','1200','2','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('13','13','Deluxe Room','500','3000','23','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('14','14','Luxury Room','600','2400','44','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('15','15','Luxury Room','600','1200',NULL,'Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('16','16','Luxury Room','600','1200','1','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('17','17','Simple Room','300','900','20A','Keith','123','ad');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('18','18','Family Room','600','1800',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('19','19','Couple Room','1200','2400',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('20','20','Family Room','3600','3600',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('21','21','Deluxe Room','1800','1800','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('22','22','Couple Room','1200','4800',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('23','23','Couple Room','1200','36000','1','keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('24','24','Family Room','3600','14400',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('25','25','Family Room','3600','3600','2A','keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('26','26','Deluxe Room','1800','1800',NULL,'keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('27','27','Family Room','3600','3600','3A','keiths','123454','Brgy. La Paz, San Marcelino, Zambales');
INSERT INTO `booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`) VALUES ('28','28','Couple Room','5000','5000','10','keiths','123454','Brgy. La Paz, San Marcelino, Zambales');

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
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `booking_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`),
  CONSTRAINT `booking_order_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('1','2','3','2022-07-20','2022-07-21','0',NULL,'pending','ORD_21055700',NULL,'0','pending',NULL,NULL,'2022-07-20 01:50:12');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('2','2','3','2022-07-20','2022-07-22','1',NULL,'booked','ORD_24215693','20220720111212800110168128204225279','600','TXN_SUCCESS','Txn Success',NULL,'2022-07-20 02:14:44');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('3','2','3','2022-07-20','2022-07-26','0','1','cancelled','ORD_26312547','20220720111212800110168165603901976','1800','TXN_SUCCESS','Txn Success',NULL,'2022-07-20 02:19:00');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('4','2','6','2022-07-20','2022-07-25','0',NULL,'payment failed','ORD_28394638','20220720111212800110168372503893816','4500','TXN_FAILURE','Your payment has been declined by your bank. Please try again or use a different method to complete the payment.',NULL,'2022-07-20 02:30:52');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('5','2','6','2022-07-20','2022-07-21','0','1','cancelled','ORD_22877860','20220720111212800110168627705312020','900','TXN_SUCCESS','Txn Success',NULL,'2022-07-20 02:32:09');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('6','2','6','2022-07-20','2022-07-28','1',NULL,'booked','ORD_28689687','20220720111212800110168303704048087','7200','TXN_SUCCESS','Txn Success','1','2022-07-20 02:34:46');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('7','2','6','2022-07-29','2022-07-30','0',NULL,'pending','ORD_24272313',NULL,'0','pending',NULL,NULL,'2022-07-29 01:13:42');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('8','2','3','2022-08-14','2022-08-16','0','1','cancelled','ORD_22541670','20220814111212800110168092803967754','600','TXN_SUCCESS','Txn Success',NULL,'2022-08-14 01:16:05');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('9','2','5','2022-08-15','2022-08-20','1',NULL,'booked','ORD_25267746','20220815111212800110168656003990120','3000','TXN_SUCCESS','Txn Success','1','2022-08-15 19:32:05');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('10','2','5','2022-08-18','2022-08-21','1',NULL,'booked','ORD_27668816','20220815111212800110168905703947446','1800','TXN_SUCCESS','Txn Success','1','2022-08-15 19:32:58');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('11','2','6','2022-08-20','2022-08-23','1',NULL,'booked','ORD_25750549','20220820111212800110168431303975409','2700','TXN_SUCCESS','Txn Success','1','2022-08-20 00:19:57');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('12','2','3','2022-08-20','2022-08-24','1',NULL,'booked','ORD_2445093','20220820111212800110168173403969278','1200','TXN_SUCCESS','Txn Success','1','2022-08-20 00:20:23');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('13','2','4','2022-08-20','2022-08-26','1',NULL,'booked','ORD_29233995','20220820111212800110168584503978338','3000','TXN_SUCCESS','Txn Success','1','2022-08-20 00:20:45');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('14','2','5','2022-08-20','2022-08-24','1',NULL,'booked','ORD_28902800','20220820111212800110168816503988359','2400','TXN_SUCCESS','Txn Success','1','2022-08-20 00:21:06');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('15','2','5','2022-08-25','2022-08-27','0','1','cancelled','ORD_2240367','20220825111212800110168807304010818','1200','TXN_SUCCESS','Txn Success',NULL,'2019-08-21 01:51:28');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('16','2','5','2022-08-26','2022-08-28','1',NULL,'booked','ORD_28784829','20220825111212800110168627505415606','1200','TXN_SUCCESS','Txn Success','1','2022-08-25 01:52:04');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('17','2','3','2022-09-08','2022-09-11','1',NULL,'booked','ORD_21289330','20220908111212800110168809204050263','900','TXN_SUCCESS','Txn Success','0','2022-09-08 01:15:30');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('18','5','5','2025-08-26','2025-08-29','0',NULL,'pending','ORD_54483360',NULL,'0','pending',NULL,NULL,'2025-08-26 18:55:14');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('19','5','3','2025-08-28','2025-08-30','0',NULL,'pending','ORD_58527336',NULL,'0','pending',NULL,NULL,'2025-08-28 12:25:57');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('20','5','5','2025-08-28','2025-08-29','0',NULL,'pending','ORD_53073650',NULL,'0','pending',NULL,NULL,'2025-08-28 17:43:39');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('21','5','4','2025-09-09','2025-09-10','1',NULL,'booked','ORD_57915932','TEST_16602','1800','TXN_SUCCESS','TEST MODE','1','2025-09-09 19:06:59');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('22','5','3','2025-09-09','2025-09-13','0','1','cancelled','ORD_5726174','TEST_85066','4800','TXN_SUCCESS','TEST MODE',NULL,'2025-09-09 19:17:45');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('23','5','3','2025-09-09','2025-10-09','1','1','cancelled','ORD_53191558','TEST_96727','36000','TXN_SUCCESS','TEST MODE','0','2025-09-09 19:25:11');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('24','5','5','2025-09-15','2025-09-19','0','1','cancelled','ORD_57822107','TEST_47934','14400','TXN_SUCCESS','TEST MODE',NULL,'2025-09-15 18:01:40');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('25','5','5','2025-09-15','2025-09-16','1',NULL,'booked','ORD_52873868','TEST_95898','3600','TXN_SUCCESS','TEST MODE','1','2025-09-15 20:09:59');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('26','5','4','2025-09-15','2025-09-16','0','1','cancelled','ORD_53128418','TEST_70700','1800','TXN_SUCCESS','TEST MODE',NULL,'2025-09-15 20:12:06');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('27','5','5','2025-09-15','2025-09-16','1',NULL,'booked','ORD_59693908','TEST_31266','3600','TXN_SUCCESS','TEST MODE','1','2025-09-15 20:16:17');
INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`) VALUES ('28','5','3','2025-10-13','2025-10-14','0',NULL,'booked','ORD_52469060','TEST_79249','5000','TXN_SUCCESS','TEST MODE',NULL,'2025-10-13 19:18:24');

DROP TABLE IF EXISTS `carousel`;
CREATE TABLE `carousel` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(150) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `carousel` (`sr_no`,`image`) VALUES ('24','IMG_97565.png');

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

DROP TABLE IF EXISTS `facilities`;
CREATE TABLE `facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('13','IMG_43553.svg','Wifi','Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus incidunt odio quos dolore commodi repudiandae tenetur.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('14','IMG_49949.svg','Air conditioner','Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus incidunt odio quos dolore commodi repudiandae tenetur.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('15','IMG_41622.svg','Television','Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus incidunt odio quos dolore commodi repudiandae tenetur.');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('20','IMG_87758.svg','Microwave','asdsadc');
INSERT INTO `facilities` (`id`,`icon`,`name`,`description`) VALUES ('22','IMG_56868.svg','123','asdffd');

DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `features` (`id`,`name`) VALUES ('13','bedroom');
INSERT INTO `features` (`id`,`name`) VALUES ('15','kitchen');
INSERT INTO `features` (`id`,`name`) VALUES ('17','sofa');
INSERT INTO `features` (`id`,`name`) VALUES ('18','videoke');
INSERT INTO `features` (`id`,`name`) VALUES ('19','Electric Fan');
INSERT INTO `features` (`id`,`name`) VALUES ('20','fridge');
INSERT INTO `features` (`id`,`name`) VALUES ('21','billiards');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('4','14','5','2','5','1asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:25');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('5','13','4','2','3','2asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:30');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('6','12','3','2','1','3asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:34');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('8','14','5','2','5','1asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:25');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('9','12','3','2','1','3asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:34');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('10','12','3','2','1','3asdlkfj Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero ','1','2025-08-20 00:22:34');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('12','21','4','5','5','lamig pag mag isa jan boi','1','2025-09-09 19:10:20');
INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`) VALUES ('13','25','5','5','5','qesfgf','1','2025-09-15 20:11:25');

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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('52','4','13');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('53','4','15');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('56','3','13');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('57','3','15');
INSERT INTO `room_facilities` (`sr_no`,`room_id`,`facilities_id`) VALUES ('58','3','22');

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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('65','4','13');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('66','4','15');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('67','5','13');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('71','3','13');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('72','3','17');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('73','3','20');
INSERT INTO `room_features` (`sr_no`,`room_id`,`features_id`) VALUES ('74','3','21');

DROP TABLE IF EXISTS `room_images`;
CREATE TABLE `room_images` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sr_no`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('29','1','single.jpg\r\n','0');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('30','5','IMG_67842.jpg','1');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('31','4','IMG_48037.jpg','1');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('32','3','IMG_78227.jpg','1');
INSERT INTO `room_images` (`sr_no`,`room_id`,`image`,`thumb`) VALUES ('33','3','IMG_62700.jpg','0');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('1','simple room','159','58','56','12','2','asdf asd','1','1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('2','simple room 2','785','159','85','452','10','adfasdfa sd','1','1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('3','Couple Room','250','5000','10','2','1','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('4','Deluxe Room','300','1800','10','4','88','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('5','Family Room','600','3600','2','2','2','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','0');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('6','Supreme deluxe room','500','900','12','9','10','Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates impedit perspiciatis, nobis libero culpa error officiis totam?Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dic','1','1');
INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`) VALUES ('7','asc','23','2','1','1','1','f','1','1');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(50) NOT NULL,
  `site_about` varchar(250) NOT NULL,
  `shutdown` tinyint(1) NOT NULL,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`sr_no`,`site_title`,`site_about`,`shutdown`) VALUES ('1','Travelers Place','Welcome to Travelers Place, your cozy retreat in the heart of Santo Niño, San Felipe, Zambales. We’re more than just a place to stay—we’re your home away from home. With clean rooms, fun amenities, and warm hospitality, we make sure every guest feels','0');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`datentime`) VALUES ('2','Keith Eimreh Lara','Kellara0227@gmail.com','San Marcelino, Zambales','09916057372','123324','2025-08-10','keiths.jpg','123','1',NULL,NULL,'0','2024-06-12 16:05:59');
INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`datentime`) VALUES ('5','keiths','kellara@gmail.com','Brgy. La Paz, San Marcelino, Zambales','123454','2207','2025-08-26','IMG_69424.jpg','$2y$10$8ETNoOwcCk2pK6GFcHwdKOCoFxpU/IiLfqza2MWPAzkiCElTDnjDy','1',NULL,NULL,'1','2025-08-26 18:45:39');
INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`token`,`t_expire`,`status`,`datentime`) VALUES ('6','kobesakol','k...7@gmail.com','Brgy. La Paz, San Marcelino, Zambales','09075767425','2207','2025-08-26','IMG_45290.jpg','$2y$10$jhMbq38EJKbSaFWmvjzv/O/vHFEr8lFyVfgHom9a5kNAjRoDIqRza','1',NULL,NULL,'1','2025-08-26 18:47:04');

DROP TABLE IF EXISTS `user_queries`;
CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sr_no`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`) VALUES ('11','Keith Eimreh Lara','kellar@gmail.com','This is one subject','orem ipsum dolor sit amet, consectetur adipisicing elit. Quos voluptate vero sed tempore illo atque beatae asperiores, adipisci dicta quia nisi voluptates im','2025-03-11 00:00:00','1');
INSERT INTO `user_queries` (`sr_no`,`name`,`email`,`subject`,`message`,`datentime`,`seen`) VALUES ('13','keiths','kellara0227@gmail.com','mag book sana kol','fsadfasdf','2025-09-09 19:44:51','1');

SET FOREIGN_KEY_CHECKS=1;

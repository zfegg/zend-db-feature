CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `user` VALUES ('1', 'Marco Pivetta', 'adf@sdfsd.com');
INSERT INTO `user` VALUES ('2', 'Marco Pivetta', 'adf@sdfsd.com');
INSERT INTO `user` VALUES ('3', 'Marco Pivetta', 'adf@sdfsd.com');
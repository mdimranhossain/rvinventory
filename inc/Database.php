<?php
$create_table = "CREATE TABLE IF NOT EXISTS `{$table}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `year` int(10) COLLATE utf8_unicode_ci DEFAULT NULL,
    `make` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    `model` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `mileage` int(10) COLLATE utf8_unicode_ci DEFAULT NULL,
    `salePrice` decimal(20,2) DEFAULT 0.00,
    `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
    `rvCategory` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
    `featuredImage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `gallery` text COLLATE utf8_unicode_ci DEFAULT NULL,
    `featuredid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `galleryfiles` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `status` int(11) DEFAULT 0,
    `createdBy` int(11) DEFAULT 0,
    `createdAt` datetime DEFAULT current_timestamp(),
    `updatedBy` int(11) DEFAULT 0,
    `updatedAt` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

  $create_imageTable = "CREATE TABLE IF NOT EXISTS `{$imageTable}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
    `mime` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
    `size` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
    `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `file_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
    `inventory_id` int(11) DEFAULT NULL,
    `attachment` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
    `display_order` int(5) DEFAULT NULL,
    `uploadedBy` int(11) DEFAULT NULL,
    `uploadedAt` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
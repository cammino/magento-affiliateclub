<?php

$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('affiliateclub')};
CREATE TABLE `affiliateclub` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `indicator_id` int(11) DEFAULT NULL,
  `indicator_email` varchar(255) NOT NULL DEFAULT '',
  `indicator_coupon` varchar(255) DEFAULT NULL,
  `indicated_id` int(11) DEFAULT NULL,
  `indicated_email` varchar(255) DEFAULT NULL,
  `indicated_coupon` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_increment_id` int(11) DEFAULT NULL,  
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
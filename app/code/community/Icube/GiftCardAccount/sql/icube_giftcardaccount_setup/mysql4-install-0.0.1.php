<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->run("CREATE TABLE `{$this->getTable('icube_giftcardaccount')}` (
                    `giftcardaccount_id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY,
                    `code` varchar(50) NOT NULL,
                    `status` tinyint(1) NOT NULL,
                    `date_created` date NOT NULL,
                    `date_expires` date DEFAULT NULL,
                    `website_id` smallint(5) NOT NULL,
                    `balance` decimal(12,4) NOT NULL default '0.0000'
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();

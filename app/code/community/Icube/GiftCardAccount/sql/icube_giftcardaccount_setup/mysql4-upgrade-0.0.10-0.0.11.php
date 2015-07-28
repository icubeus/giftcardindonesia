<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('icube_giftcardaccount/history')}` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `giftcardaccount_id` int(10) unsigned NOT NULL DEFAULT 0,
  `updated_at` datetime NULL DEFAULT NULL,
  `action` tinyint(3) unsigned NOT NULL default '0',
  `balance_amount` decimal(12,4) unsigned NOT NULL DEFAULT 0,
  `balance_delta` decimal(12,4) NOT NULL DEFAULT 0,
  `additional_info` tinytext COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_GIFTCARDACCOUNT_HISTORY_ACCOUNT',
    $installer->getTable('icube_giftcardaccount/history'), 'giftcardaccount_id',
    $installer->getTable('icube_giftcardaccount/giftcardaccount'), 'giftcardaccount_id'
);

$installer->endSetup();

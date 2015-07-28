<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('icube_giftcardaccount_pool')}` (
    `code` varchar(255) NOT NULL PRIMARY KEY,
    `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->changeColumn(
    $this->getTable('icube_giftcardaccount'),
    'code',
    'code',
    'VARCHAR(255) NOT NULL'
);

$installer->endSetup();

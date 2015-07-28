<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('icube_giftcardaccount'), 'is_redeemable', 'tinyint(1) NOT NULL DEFAULT 1');

$installer->endSetup();

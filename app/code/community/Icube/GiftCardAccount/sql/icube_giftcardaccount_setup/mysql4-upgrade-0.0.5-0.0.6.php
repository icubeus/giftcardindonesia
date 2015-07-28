<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->addAttribute('quote_address', 'used_gift_cards', array('type'=>'text'));

$installer->getConnection()->changeColumn($this->getTable('sales_flat_quote_address'),
    'used_gift_cards', 'used_gift_cards',
    'text CHARACTER SET utf8'
);

$installer->endSetup();

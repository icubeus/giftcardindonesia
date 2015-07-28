<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->addAttribute('order', 'gift_cards', array('type'=>'text'));

$installer->addAttribute('order', 'base_gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'gift_cards_amount', array('type'=>'decimal'));

$installer->endSetup();

<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->addAttribute('quote', 'gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_gift_cards_amount', array('type'=>'decimal'));

$installer->addAttribute('quote_address', 'gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'base_gift_cards_amount', array('type'=>'decimal'));

$installer->addAttribute('quote', 'gift_cards_amount_used', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_gift_cards_amount_used', array('type'=>'decimal'));

$installer->endSetup();

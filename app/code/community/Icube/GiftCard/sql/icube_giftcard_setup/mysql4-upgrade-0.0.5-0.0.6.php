<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->updateAttribute('catalog_product', 'giftcard_type', 'source_model', 'icube_giftcard/source_type');

$installer->endSetup();

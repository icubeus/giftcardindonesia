<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->updateAttribute('catalog_product', 'allow_open_amount', 'backend_model', '');

$installer->endSetup();

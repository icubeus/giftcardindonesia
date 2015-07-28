<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE {$installer->getTable('icube_giftcard_amount')} (
  `value_id` int(11) NOT NULL auto_increment,
  `website_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_type_id` smallint (5) unsigned NOT NULL,
  `attribute_id` smallint (5) unsigned NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_GIFTCARD_AMOUNT_PRODUCT_ENTITY` (`entity_id`),
  KEY `FK_GIFTCARD_AMOUNT_WEBSITE` (`website_id`),
  KEY `FK_GIFTCARD_AMOUNT_ATTRIBUTE_ID` (`attribute_id`),
  CONSTRAINT `FK_GIFTCARD_AMOUNT_PRODUCT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_GIFTCARD_AMOUNT_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_GIFTCARD_AMOUNT_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

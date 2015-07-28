<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->getConnection()
    ->update($installer->getTable('catalog/product'),
        array(
            'has_options' => 1,
            'required_options' => 1
        ),
        $installer->getConnection()->quoteInto('type_id=?', Icube_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD)
    );

$installer->endSetup();

<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('icube_giftcardaccount'), 'distribution_id', 'varchar(255) NOT NULL UNIQUE');

//$installer->getConnection()->addIndex(
//    $installer->getTable('icube_giftcardaccount'),
//    $installer->getIdxName(
//        'icube_giftcardaccount',
//        array('distribution_id'),
//        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
//    ),
//    array('distribution_id'),
//    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
//);

$installer->endSetup();
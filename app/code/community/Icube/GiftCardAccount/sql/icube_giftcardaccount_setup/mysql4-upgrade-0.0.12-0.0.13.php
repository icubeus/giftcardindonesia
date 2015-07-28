<?php
$installer = $this;
/* @var $installer Icube_GiftCardAccount_Model_Mysql4_Setup */

$tableGCA     = $installer->getTable('icube_giftcardaccount/giftcardaccount');
$tableWebsite = $installer->getTable('core/website');

// drop orphan GCAs, modify website_id field to make it compatible with foreign key
$installer->run("DELETE FROM {$tableGCA} WHERE website_id NOT IN (SELECT website_id FROM {$tableWebsite})");
$installer->getConnection()->changeColumn($tableGCA, 'website_id', 'website_id',
    'smallint(5) UNSIGNED NOT NULL DEFAULT 0'
);
$installer->getConnection()->addConstraint('FK_GIFTCARDACCOUNT_WEBSITE_ID', $tableGCA, 'website_id',
    $tableWebsite, 'website_id', 'CASCADE', 'CASCADE'
);

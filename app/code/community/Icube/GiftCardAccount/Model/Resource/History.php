<?php
/**
 * GiftCard account history serource model
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCardAccount_Model_Resource_History extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary key field
     *
     */
    protected function _construct()
    {
        $this->_init('icube_giftcardaccount/history', 'history_id');
    }

    /**
     * Setting "updated_at" date before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Icube_GiftCardAccount_Model_Resource_History
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));

        return parent::_beforeSave($object);
    }
}

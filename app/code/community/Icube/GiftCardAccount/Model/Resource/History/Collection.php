<?php
/**
 * GiftCardAccount History Resource Collection
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCardAccount_Model_Resource_History_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('icube_giftcardaccount/history');
    }
}

<?php
/**
 * GiftCardAccount Pool Resource Model Collection
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCardAccount_Model_Resource_Pool_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('icube_giftcardaccount/pool');
    }
}

<?php
/**
 * GiftCardAccount Resource Collection
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCardAccount_Model_Resource_Giftcardaccount_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init('icube_giftcardaccount/giftcardaccount');
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Icube_GiftCardAccount_Model_Resource_Giftcardaccount_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }
}

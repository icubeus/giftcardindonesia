<?php
/**
 * Customerbalance history collection
 *
 * @category    Icube
 * @package     Icube_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_CustomerBalance_Model_Resource_Balance_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('icube_customerbalance/balance');
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Icube_CustomerBalance_Model_Resource_Balance_Collection
     */
    public function addWebsitesFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }

    /**
     * Implement after load logic for each collection item
     *
     * @return Icube_CustomerBalance_Model_Resource_Balance_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
}

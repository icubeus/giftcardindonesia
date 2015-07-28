<?php
/**
 * Balance history collection
 *
 * @category    Icube
 * @package     Icube_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_CustomerBalance_Model_Resource_Balance_History_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('icube_customerbalance/balance_history');
    }

    /**
     * Instantiate select joined to balance
     *
     * @return Icube_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinInner(array('b' => $this->getTable('icube_customerbalance/balance')),
                'main_table.balance_id = b.balance_id', array('customer_id'         => 'b.customer_id',
                                                              'website_id'          => 'b.website_id',
                                                              'base_currency_code'  => 'b.base_currency_code'))
        ;
        return $this;
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Icube_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    public function addWebsitesFilter($websiteIds)
    {
        $this->getSelect()->where('b.website_id IN (?)', $websiteIds);
        return $this;
    }

    /**
     * Retrieve history data
     *
     * @param  string $customerId
     * @param string|null $websiteId
     * @return Icube_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    public function loadHistoryData($customerId, $websiteId = null)
    {
        $this->addFieldToFilter('customer_id', $customerId)
                ->addOrder('updated_at', 'DESC')
                ->addOrder('history_id', 'DESC');
        if (!empty($websiteId)) {
            $this->getSelect()->where('b.website_id IN (?)', $websiteId);
        }
        return $this;
    }
}

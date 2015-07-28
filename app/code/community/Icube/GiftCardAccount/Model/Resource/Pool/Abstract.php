<?php
/**
 * GiftCardAccount Pool Resource Model Abstract
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Icube_GiftCardAccount_Model_Resource_Pool_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Delete records in db using specified status as criteria
     *
     * @param int $status
     * @return Icube_GiftCardAccount_Model_Resource_Pool_Abstract
     */
    public function cleanupByStatus($status)
    {
        $where = array('status = ?' => $status);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}

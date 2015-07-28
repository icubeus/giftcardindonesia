<?php
/**
 * Customerbalance helper
 *
 */
class Icube_CustomerBalance_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML configuration paths
     */
    const XML_PATH_ENABLED     = 'customer/icube_customerbalance/is_enabled';
    const XML_PATH_AUTO_REFUND = 'customer/icube_customerbalance/refund_automatically';

    /**
     * Check whether customer balance functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED) == 1;
    }

    /**
     * Check if automatically refund is enabled
     *
     * @return bool
     */
    public function isAutoRefundEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_AUTO_REFUND);
    }
}

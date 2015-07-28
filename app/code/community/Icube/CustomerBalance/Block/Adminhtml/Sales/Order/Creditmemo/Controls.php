<?php
/**
 * Refund to customer balance functionality block
 *
 */
class Icube_CustomerBalance_Block_Adminhtml_Sales_Order_Creditmemo_Controls
 extends Mage_Core_Block_Template
{
    /**
     * Check whether refund to customerbalance is available
     *
     * @return bool
     */
    public function canRefundToCustomerBalance()
    {
        if ($this->_getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }

    /**
     * Check whether real amount can be refunded to customer balance
     *
     * @return bool
     */
    public function canRefundMoneyToCustomerBalance()
    {
        if ($this->_getCreditmemo()->getGrandTotal()) {
            return false;
        }

        if ($this->_getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }

    /**
     * Pre Populate amount to be refunded to customerbalance
     *
     * @return float
     */
    public function getReturnValue()
    {
        $max = $this->_getCreditmemo()->getCustomerBalanceReturnMax();

        //We want to subtract the reward points when returning to the customer
        $rewardCurrencyBalance = $this->_getCreditmemo()->getRewardCurrencyAmount();

        if ($rewardCurrencyBalance > 0 && $rewardCurrencyBalance < $max) {
            $max = $max - $rewardCurrencyBalance;
        }

        if ($max) {
            return $max;
        }
        return 0;
    }

    /**
     * Fetches the Credit Memo Object
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }
}

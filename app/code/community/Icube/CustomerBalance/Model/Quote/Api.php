<?php
/**
 * Store credit for quote API
 *
 * @category   Icube
 * @package    Icube_CustomerBalance
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Icube_CustomerBalance_Model_Quote_Api extends Mage_Checkout_Model_Api_Resource
{

    /**
     * Set amount from customer store credit into shopping cart (quote)
     *
     * @param  string $quoteId
     * @param  string|int $store
     * @return float - used customer balance amount
     */
    public function setAmount($quoteId, $store = null)
    {
        return $this->_setUseStoreCreditForQuote($quoteId, $store);
    }

    /**
     * Remove amount from shopping cart (quote) and increase customer store credit
     *
     * @param int $quoteId
     * @param  string|int $store
     * @return bool
     */
    public function removeAmount($quoteId, $store = null)
    {
        $isRemoved = false;
        if ($this->_setUseStoreCreditForQuote($quoteId, $store, false) == 0) {
            $isRemoved = true;
        }
        return $isRemoved;
    }

    /**
     * Set/unset usage of store credit for quote
     *
     * @param  string $quoteId
     * @param string|int $store
     * @param bool $shouldUseCustomerBalance
     *
     * @return float - used customer balance amount
     */
    protected function _setUseStoreCreditForQuote($quoteId, $store = null, $shouldUseCustomerBalance = true)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteId, $store);
        if (!$quote->getCustomerId()) {
            $this->_fault('guest_quote');
        }
        $quote->setUseCustomerBalance($shouldUseCustomerBalance);
        $payment = $quote->getPayment();
        /** @var $saveTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = Mage::getModel('core/resource_transaction');
        if ($shouldUseCustomerBalance) {
            $balance = Mage::getModel('icube_customerbalance/balance')
                    ->setCustomerId($quote->getCustomerId())
                    ->setWebsiteId(Mage::app()->getStore($quote->getStoreId())->getWebsiteId())
                    ->loadByCustomer();
            if ($balance) {
                $quote->setCustomerBalanceInstance($balance);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                    $saveTransaction->addObject($payment);
                }
            } else {
                $quote->setUseCustomerBalance(false);
            }
        }
        $quote->collectTotals();
        try {
            $saveTransaction->addObject($quote)->save();
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return (float)$quote->getCustomerBalanceAmountUsed();
    }

}

<?php
class Icube_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional extends Mage_Core_Block_Template
{
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getAppliedGiftCardAmount()
    {
        return $this->_getQuote()->getBaseGiftCardsAmountUsed();
    }

    public function isFullyPaidAfterApplication()
    {
        // TODO remove dependences to other modules
        if ($this->_getQuote()->getBaseGrandTotal() > 0 || $this->_getQuote()->getCustomerBalanceAmountUsed() > 0 || $this->_getQuote()->getRewardPointsBalance() > 0) {
            return false;
        }

        return true;
    }
}

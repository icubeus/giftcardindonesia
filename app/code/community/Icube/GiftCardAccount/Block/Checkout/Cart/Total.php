<?php
class Icube_GiftCardAccount_Block_Checkout_Cart_Total extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'giftcardaccount/cart/total.phtml';

    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return Mage::helper('icube_giftcardaccount')->getCards($this->_getQuote());
    }
}

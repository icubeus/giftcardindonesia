<?php
class Icube_GiftCardAccount_Block_Adminhtml_Sales_Order_Creditmemo_Controls
 extends Mage_Core_Block_Template
{
    public function canRefundToCustomerBalance()
    {
        if (!Mage::registry('current_creditmemo')->getGiftCardsAmount()) {
            return false;
        }

        if (Mage::registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }
}

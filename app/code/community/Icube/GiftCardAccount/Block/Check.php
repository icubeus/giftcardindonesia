<?php
/**
 * Check result block for a Giftcardaccount
 *
 */
class Icube_GiftCardAccount_Block_Check extends Mage_Core_Block_Template
{
    /**
     * Get current card instance from registry
     *
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function getCard()
    {
        return Mage::registry('current_giftcardaccount');
    }

    /**
     * Check whether a gift card account code is provided in request
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getRequest()->getParam('giftcard_code', '');
    }
}

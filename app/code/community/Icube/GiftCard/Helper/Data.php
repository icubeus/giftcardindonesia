<?php
/**
 * Giftcard module helper
 */
class Icube_GiftCard_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Instantiate giftardaccounts block when a gift card email should be sent
     * @return Mage_Core_Block_Template
     */
    public function getEmailGeneratedItemsBlock()
    {
        $className = Mage::getConfig()->getBlockClassName('core/template');
        $block = new $className;
        if (Mage::app()->getStore()->isAdmin()) {
            $block->setTemplate('icube/giftcard/email/generated.phtml');
            $block->setArea('adminhtml');
        } else {
            $block->setTemplate('giftcard/email/generated.phtml');
        }
        return $block;
    }
}

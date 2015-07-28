<?php
/**
 * GiftCard product view form
 *
 * @category   Icube
 * @package    Icube_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCard_Block_Catalog_Product_View_Type_Giftcard_Form extends Mage_Core_Block_Template
{
    /**
     * Check if email is available
     *
     * @return bool
     */
    public function isEmailAvailable()
    {
        $emailNeeded = $this->getEmailNeeded();
        if ($emailNeeded === null) {
            $product = Mage::registry('current_product');
            if (!$product) {
                return false;
            }
            $emailNeeded = !$product->getTypeInstance()->isTypePhysical();
            $this->setEmailNeeded($emailNeeded);
        }
        return (bool)$emailNeeded;
    }

    /**
     * Get customer name from session
     *
     * @return string
     */
    public function getCustomerName()
    {
        $firstName = (string)Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
        $lastName  = (string)Mage::getSingleton('customer/session')->getCustomer()->getLastname();

        if ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        } else {
            return '';
        }
    }

    /**
     * Get customer email from session
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return (string) Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }
}

<?php
class Icube_GiftCard_Model_Source_Status extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => Mage::helper('icube_giftcard')->__('Ordered')
            ),
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => Mage::helper('icube_giftcard')->__('Invoiced')
            )
        );
    }
}

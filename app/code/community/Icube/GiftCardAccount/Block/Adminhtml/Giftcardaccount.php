<?php
class Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'icube_giftcardaccount';
        $this->_headerText = Mage::helper('icube_giftcardaccount')->__('Manage Gift Card Accounts');
        $this->_addButtonLabel = Mage::helper('icube_giftcardaccount')->__('Add Gift Card Account');
        parent::__construct();
    }
}

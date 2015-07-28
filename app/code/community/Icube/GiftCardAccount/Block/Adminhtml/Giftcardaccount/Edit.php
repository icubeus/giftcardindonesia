<?php
class Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'icube_giftcardaccount';

        parent::__construct();

        $clickSave = "\$('_sendaction').value = 0;";
        $clickSave .= "\$('_sendrecipient_email').removeClassName('required-entry');";
        $clickSave .= "\$('_sendrecipient_name').removeClassName('required-entry');";
        $clickSave .= "editForm.submit();";

        $this->_updateButton('save', 'label', Mage::helper('icube_giftcardaccount')->__('Save'));
        $this->_updateButton('save', 'onclick', $clickSave);
        $this->_updateButton('delete', 'label', Mage::helper('icube_giftcardaccount')->__('Delete'));

        $clickSend = "\$('_sendrecipient_email').addClassName('required-entry');";
        $clickSend .= "\$('_sendrecipient_name').addClassName('required-entry');";
        $clickSend .= "\$('_sendaction').value = 1;";
        $clickSend .= "editForm.submit();";

        $this->_addButton('send', array(
            'label'     => Mage::helper('icube_giftcardaccount')->__('Save & Send Email'),
            'onclick'   => $clickSend,
            'class'     => 'save',
        ));
    }

    public function getGiftcardaccountId()
    {
        return Mage::registry('current_giftcardaccount')->getId();
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_giftcardaccount')->getId()) {
            return Mage::helper('icube_giftcardaccount')->__('Edit Gift Card Account: %s', $this->escapeHtml(Mage::registry('current_giftcardaccount')->getCode()));
        }
        else {
            return Mage::helper('icube_giftcardaccount')->__('New Gift Card Account');
        }
    }

}

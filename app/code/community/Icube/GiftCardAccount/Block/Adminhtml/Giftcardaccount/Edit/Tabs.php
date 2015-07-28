<?php
class Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardaccount_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('icube_giftcardaccount')->__('Gift Card Account'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('info', array(
            'label'     => Mage::helper('icube_giftcardaccount')->__('Information'),
            'content'   => $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_edit_tab_info')->initForm()->toHtml(),
            'active'    => true
        ));

        $this->addTab('send', array(
            'label'     => Mage::helper('icube_giftcardaccount')->__('Send Gift Card'),
            'content'   => $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_edit_tab_send')->initForm()->toHtml(),
        ));

        $model = Mage::registry('current_giftcardaccount');
        if ($model->getId()) {
            $this->addTab('history', array(
                'label'     => Mage::helper('icube_giftcardaccount')->__('History'),
                'content'   => $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_edit_tab_history')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

}

<?php
class Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('icube/giftcardaccount/edit/tab/info.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_info');

        $model = Mage::registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('icube_giftcardaccount')->__('Information'))
        );

        if ($model->getId()){
            $fieldset->addField('code', 'label', array(
                'name'      => 'code',
                'label'     => Mage::helper('icube_giftcardaccount')->__('Gift Card Code'),
                'title'     => Mage::helper('icube_giftcardaccount')->__('Gift Card Code')
            ));

            $fieldset->addField('state_text', 'label', array(
                'name'      => 'state_text',
                'label'     => Mage::helper('icube_giftcardaccount')->__('Status'),
                'title'     => Mage::helper('icube_giftcardaccount')->__('Status')
            ));
        }

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('icube_giftcardaccount')->__('Active'),
            'title'     => Mage::helper('icube_giftcardaccount')->__('Active'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                Icube_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                    Mage::helper('icube_giftcardaccount')->__('Yes'),
                Icube_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                    Mage::helper('icube_giftcardaccount')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('status', Icube_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED);
        }

//        $fieldset->addField('is_redeemable', 'select', array(
//            'label'     => Mage::helper('icube_giftcardaccount')->__('Redeemable'),
//            'title'     => Mage::helper('icube_giftcardaccount')->__('Redeemable'),
//            'name'      => 'is_redeemable',
//            'required'  => true,
//            'options'   => array(
//                Icube_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE =>
//                    Mage::helper('icube_giftcardaccount')->__('Yes'),
//                Icube_GiftCardAccount_Model_Giftcardaccount::NOT_REDEEMABLE =>
//                    Mage::helper('icube_giftcardaccount')->__('No'),
//            ),
//        ));
//        if (!$model->getId()) {
//            $model->setData('is_redeemable', Icube_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE);
//        }

        $field = $fieldset->addField('website_id', 'select', array(
            'name'      => 'website_id',
            'label'     => Mage::helper('icube_giftcardaccount')->__('Website'),
            'title'     => Mage::helper('icube_giftcardaccount')->__('Website'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);

        $fieldset->addType('price', 'Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Form_Price');

        $fieldset->addField('balance', 'price', array(
            'label'     => Mage::helper('icube_giftcardaccount')->__('Balance'),
            'title'     => Mage::helper('icube_giftcardaccount')->__('Balance'),
            'name'      => 'balance',
            'class'     => 'validate-number',
            'required'  => true,
            'note'      => '<div id="balance_currency"></div><b>value must equal or greater than current balance</b>'
        ));

        if ($model->getId()){
            $fieldset->addField('date_expires', 'date', array(
                'name'   => 'date_expires',
                'label'  => Mage::helper('icube_giftcardaccount')->__('Expiration Date'),
                'title'  => Mage::helper('icube_giftcardaccount')->__('Expiration Date'),
                'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'disabled' => true
            ));
            $fieldset->addField('distribution_id', 'label', array(
                'name'      => 'distribution_id',
                'label'     => Mage::helper('icube_giftcardaccount')->__('Distribution ID'),
                'title'     => Mage::helper('icube_giftcardaccount')->__('Distribution ID'),
            ));
        } else {
            $fieldset->addField('distribution_id', 'text', array(
                'label'     => Mage::helper('icube_giftcardaccount')->__('Distribution ID'),
                'title'     => Mage::helper('icube_giftcardaccount')->__('Distribution ID'),
                'name'      => 'distribution_id',
                'required'  => true
            ));
        }

        $form->setValues($model->getData());

        $this->setForm($form);
        return $this;
    }

    public function getCurrencyJson()
    {
        $result = array();
        $websites = Mage::getSingleton('adminhtml/system_store')->getWebsiteCollection();
        foreach ($websites as $id=>$website) {
            $result[$id] = $website->getBaseCurrencyCode();
        }

        return Mage::helper('core')->jsonEncode($result);
    }
}

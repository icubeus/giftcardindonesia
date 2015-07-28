<?php
class Icube_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Form extends
    Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $prefix = '_customerbalance';
        $form->setHtmlIdPrefix($prefix);
        $form->setFieldNameSuffix('customerbalance');

        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));

        /** @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset('storecreidt_fieldset',
            array('legend' => Mage::helper('icube_customerbalance')->__('Update Balance'))
        );

        if (!Mage::getSingleton('icube_customerbalance/balance')->shouldCustomerHaveOneBalance($customer)) {
            $fieldset->addField('website_id', 'select', array(
                'name'     => 'website_id',
                'label'    => Mage::helper('icube_customerbalance')->__('Website'),
                'title'    => Mage::helper('icube_customerbalance')->__('Website'),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(),
                'onchange' => 'updateEmailWebsites()',
            ));
        }

        $fieldset->addField('amount_delta', 'text', array(
            'name'     => 'amount_delta',
            'label'    => Mage::helper('icube_customerbalance')->__('Update Balance'),
            'title'    => Mage::helper('icube_customerbalance')->__('Update Balance'),
            'comment'  => Mage::helper('icube_customerbalance')->__('An amount on which to change the balance'),
        ));

        $fieldset->addField('notify_by_email', 'checkbox', array(
            'name'     => 'notify_by_email',
            'label'    => Mage::helper('icube_customerbalance')->__('Notify Customer by Email'),
            'title'    => Mage::helper('icube_customerbalance')->__('Notify Customer by Email'),
            'after_element_html' => '<script type="text/javascript">'
                . "
                updateEmailWebsites();
                $('{$prefix}notify_by_email').disableSendemail = function() {
                    $('{$prefix}store_id').disabled = (this.checked) ? false : true;
                }.bind($('{$prefix}notify_by_email'));
                Event.observe('{$prefix}notify_by_email', 'click', $('{$prefix}notify_by_email').disableSendemail);
                $('{$prefix}notify_by_email').disableSendemail();
                "
                . '</script>'
        ));

        $field = $fieldset->addField('store_id', 'select', array(
            'name'     => 'store_id',
            'label'    => Mage::helper('icube_customerbalance')->__('Send Email Notification From the Following Store View'),
            'title'    => Mage::helper('icube_customerbalance')->__('Send Email Notification From the Following Store View'),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);

        $fieldset->addField('comment', 'text', array(
            'name'     => 'comment',
            'label'    => Mage::helper('icube_customerbalance')->__('Comment'),
            'title'    => Mage::helper('icube_customerbalance')->__('Comment'),
            'comment'  => Mage::helper('icube_customerbalance')->__('Comment'),
        ));

        if ($customer->isReadonly()) {
            if ($form->getElement('website_id')) {
                $form->getElement('website_id')->setReadonly(true, true);
            }
            $form->getElement('store_id')->setReadonly(true, true);
            $form->getElement('amount_delta')->setReadonly(true, true);
            $form->getElement('notify_by_email')->setReadonly(true, true);
        }

        $form->setValues($customer->getData());
        $this->setForm($form);


        return $this;
    }
}

<?php
class Icube_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_Giftcard
 extends Mage_Adminhtml_Block_Widget
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _construct()
    {
        $this->setTemplate('icube/giftcard/catalog/product/edit/tab/giftcard.phtml');
        parent::_construct();
    }
    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('icube_giftcard')->__('Gift Card Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('icube_giftcard')->__('Gift Card Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return true when current product is new
     *
     * @return bool
     */
    public function isNew()
    {
        if (Mage::registry('product')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Return field name prefix
     *
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'product';
    }

    /**
     * Get current product value, when no value available - return config value
     *
     * @param string $field
     * @return string
     */
    public function getFieldValue($field)
    {
        if (!$this->isNew()) {
            return Mage::registry('product')->getDataUsingMethod($field);
        }

        return Mage::getStoreConfig(Icube_GiftCard_Model_Giftcard::XML_PATH . $field);
    }

    /**
     * Return gift card types
     *
     * @return array
     */
    public function getCardTypes()
    {
        return array(
            Icube_GiftCard_Model_Giftcard::TYPE_VIRTUAL  => Mage::helper('icube_giftcard')->__('Virtual'),
            Icube_GiftCard_Model_Giftcard::TYPE_PHYSICAL => Mage::helper('icube_giftcard')->__('Physical'),
            Icube_GiftCard_Model_Giftcard::TYPE_COMBINED => Mage::helper('icube_giftcard')->__('Combined'),
        );
    }

    /**
     * Return email template select options
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        $result = array();
        $template = Mage::getModel('adminhtml/system_config_source_email_template');
        $template->setPath(Icube_GiftCard_Model_Giftcard::XML_PATH_EMAIL_TEMPLATE);
        foreach ($template->toOptionArray() as $one) {
            $result[$one['value']] = $this->escapeHtml($one['label']);
        }
        return $result;
    }

    public function getConfigValue($field)
    {
        return Mage::getStoreConfig(Icube_GiftCard_Model_Giftcard::XML_PATH . $field);
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return Mage::registry('product')->getGiftCardReadonly();
    }
}

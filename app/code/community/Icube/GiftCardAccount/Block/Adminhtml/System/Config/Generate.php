<?php
class Icube_GiftCardAccount_Block_Adminhtml_System_Config_Generate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('icube/giftcardaccount/config/generate.phtml');
        }
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Return code pool usage
     *
     * @return Varien_Object
     */
    public function getUsage()
    {
        return Mage::getModel('icube_giftcardaccount/pool')->getPoolUsageInfo();
    }
}

<?php
class Icube_GiftCard_Block_Adminhtml_Renderer_Amount
 extends Mage_Adminhtml_Block_Widget
 implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element = null;
    protected $_websites = null;

    public function __construct()
    {
        $this->setTemplate('icube/giftcard/renderer/amount.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     *  Render Amounts Element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $isAddButtonDisabled = ($element->getData('readonly_disabled') === true) ? true : false;
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('icube_giftcard')->__('Add Amount'),
                    'onclick'   => "giftcardAmountsControl.addItem('" . $this->getElement()->getHtmlId() . "')",
                    'class'     => 'add',
                    'disabled'  => $isAddButtonDisabled
                )));

        return $this->toHtml();
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    public function isMultiWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'      => $this->__('All Websites'),
            'currency'  => Mage::app()->getBaseCurrencyCode()
        );

        if (!Mage::app()->isSingleStoreMode() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            if ($storeId = $this->getProduct()->getStoreId()) {
                $website = Mage::app()->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                );
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name'      => $website->getName(),
                        'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                    );
                }
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();

        if (is_array($data) && count($data)) {
            usort($data, array($this, '_sortValues'));
            $values = $data;
        }
        return $values;
    }

    protected function _sortValues($a, $b)
    {
        if ($a['website_id']!=$b['website_id']) {
            return $a['website_id']<$b['website_id'] ? -1 : 1;
        }
        return 0;
    }
}

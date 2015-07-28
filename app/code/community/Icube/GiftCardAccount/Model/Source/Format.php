<?php
class Icube_GiftCardAccount_Model_Source_Format extends Mage_Core_Model_Abstract
{
    /**
     * Return list of gift card account code formats
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            Icube_GiftCardAccount_Model_Pool::CODE_FORMAT_ALPHANUM
                => Mage::helper('icube_giftcardaccount')->__('Alphanumeric'),
            Icube_GiftCardAccount_Model_Pool::CODE_FORMAT_ALPHA
                => Mage::helper('icube_giftcardaccount')->__('Alphabetical'),
            Icube_GiftCardAccount_Model_Pool::CODE_FORMAT_NUM
                => Mage::helper('icube_giftcardaccount')->__('Numeric'),
        );
    }

    /**
     * Return list of gift card account code formats as options array.
     * If $addEmpty true - add empty option
     *
     * @param boolean $addEmpty
     * @return array
     */
    public function toOptionArray($addEmpty = false)
    {
        $result = array();

        if ($addEmpty) {
            $result[] = array('value' => '',
                              'label' => '');
        }

        foreach ($this->getOptions() as $value=>$label) {
            $result[] = array('value' => $value,
                              'label' => $label);
        }

        return $result;
    }
}

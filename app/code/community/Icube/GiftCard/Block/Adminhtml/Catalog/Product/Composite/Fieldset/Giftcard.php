<?php
class Icube_GiftCard_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Giftcard
    extends Icube_GiftCard_Block_Catalog_Product_View_Type_Giftcard
{
    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     */
    public function getIsLastFieldset()
    {
        if ($this->hasData('is_last_fieldset')) {
            return $this->getData('is_last_fieldset');
        } else {
            return !$this->getProduct()->getOptions();
        }
    }
}

<?php
class Icube_GiftCard_Model_CatalogIndex_Data_Giftcard extends Mage_CatalogIndex_Model_Data_Simple
{
    protected $_haveChildren = false;

    protected function _construct()
    {
        $this->_init('icube_giftcard/catalogindex_data_giftcard');
    }

    /**
     * Retreive product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Icube_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD;
    }


    /**
     * Fetch final price for product
     *
     * @param int $product
     * @param Mage_Core_Model_Store $store
     * @param Mage_Customer_Model_Group $group
     * @return float
     */
    public function getFinalPrice($product, $store, $group)
    {
        $finalPrice = false;
        $allowOpen = $minAmount = 0;

        $minAmountId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'open_amount_min');
        $allowOpenId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'allow_open_amount');

        $attributes = array($minAmountId, $allowOpenId);

        $productData = $this->getAttributeData($product, $attributes, $store);
        foreach ($productData as $row) {
            switch ($row['attribute_id']) {
                case $allowOpenId:
                    $allowOpen = $row['value'];
                break;
                case $minAmountId:
                    $minAmount = $row['value'];
                break;
            }
        }

        if ($allowOpen && $minAmount) {
            $finalPrice = $minAmount;
        }

        $amounts = $this->getResource()->getAmounts($product, $store);

        if (is_array($amounts) && $amounts) {
            sort($amounts);
            $minAvailableAmount = $amounts[0];
            if ($finalPrice === false) {
                $finalPrice = $minAvailableAmount;
            } else {
                $finalPrice = min($minAvailableAmount, $finalPrice);
            }            
        }
        return $finalPrice;
    }
}

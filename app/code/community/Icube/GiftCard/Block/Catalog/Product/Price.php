<?php
class Icube_GiftCard_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /**
     * @deprecated See GiftCard price model
     * @var array
     */
    protected $_amountCache = array();

    /**
     * @deprecated See GiftCard price model
     * @var array
     */
    protected $_minMaxCache = array();

    /**
     * Return minimal amount for Giftcard product using price model
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMinAmount($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $product->getPriceModel()->getMinAmount($product);
    }

    /**
     * Return maximal amount for Giftcard product using price model
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMaxAmount($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $product->getPriceModel()->getMaxAmount($product);
    }

    /**
     * @deprecated See GiftCard price model
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _calcMinMax($product = null)
    {
        return array('min' => $this->getMinAmount($product), 'max' => $this->getMaxAmount($product));
    }

    /**
     * @deprecated See GiftCard price model
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getAmounts($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $product->getPriceModel()->getSortedAmounts($product);
    }
}

<?php
/**
 * Catalog data index model for giftcards
 *
 * @category    Icube
 * @package     Icube_GiftCard
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCard_Model_Resource_Catalogindex_Data_Giftcard
    extends Mage_CatalogIndex_Model_Resource_Data_Abstract
{
    /**
     * Amounts cache
     *
     * @var array
     */
    protected $_cache  = array();

    /**
     * Get amounts by product and store
     *
     * @param int $product
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getAmounts($product, $store)
    {
        $isGlobal = ($store->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE)
                == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);

        if ($isGlobal) {
            $key = $product;
        } else {
            $website = $store->getWebsiteId();
            $key = "{$product}|{$website}";
        }

        $read = $this->_getReadAdapter();
        if (!isset($this->_cache[$key])) {
            $select = $read->select()
                ->from($this->getTable('icube_giftcard/amount'), array('value', 'website_id'))
                ->where('entity_id = ?', $product);
            $bind = array(
                'product_id' => $product
            );
            if ($isGlobal) {
                $select->where('website_id = 0');
            } else {
                $select->where('website_id IN (0, :website_id)');
                $bind['website_id'] = $website;
            }
            $fetched = $read->fetchAll($select, $bind);
            $this->_cache[$key] = $this->_convertPrices($fetched, $store);
        }
        return $this->_cache[$key];
    }

    /**
     * Convert amounts to store base currency
     *
     * @param array $amounts
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    protected function _convertPrices($amounts, $store)
    {
        $result = array();
        if (is_array($amounts) && $amounts) {
            foreach ($amounts as $amount) {
                $value = $amount['value'];
                if ($amount['website_id'] == 0) {
                    $rate = $store->getBaseCurrency()->getRate(Mage::app()->getBaseCurrencyCode());
                    if ($rate) {
                        $value = $value / $rate;
                    } else {
                        continue;
                    }
                }
                $result[] = $value;
            }
        }
        return $result;
    }
}

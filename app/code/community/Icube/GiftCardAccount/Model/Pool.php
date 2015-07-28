<?php
/**
 * Enter description here ...
 *
 * @method Icube_GiftCardAccount_Model_Resource_Pool _getResource()
 * @method Icube_GiftCardAccount_Model_Resource_Pool getResource()
 * @method string getCode()
 * @method Icube_GiftCardAccount_Model_Pool setCode(string $value)
 * @method int getStatus()
 * @method Icube_GiftCardAccount_Model_Pool setStatus(int $value)
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCardAccount_Model_Pool extends Icube_GiftCardAccount_Model_Pool_Abstract
{
    const CODE_FORMAT_ALPHANUM = 'alphanum';
    const CODE_FORMAT_ALPHA = 'alpha';
    const CODE_FORMAT_NUM = 'num';

    const XML_CONFIG_CODE_FORMAT = 'giftcard/giftcardaccount_general/code_format';
    const XML_CONFIG_CODE_LENGTH = 'giftcard/giftcardaccount_general/code_length';
    const XML_CONFIG_CODE_PREFIX = 'giftcard/giftcardaccount_general/code_prefix';
    const XML_CONFIG_CODE_SUFFIX = 'giftcard/giftcardaccount_general/code_suffix';
    const XML_CONFIG_CODE_SPLIT  = 'giftcard/giftcardaccount_general/code_split';
    const XML_CONFIG_POOL_SIZE   = 'giftcard/giftcardaccount_general/pool_size';
    const XML_CONFIG_POOL_THRESHOLD = 'giftcard/giftcardaccount_general/pool_threshold';

    const XML_CHARSET_NODE      = 'global/icube/giftcardaccount/charset/%s';
    const XML_CHARSET_SEPARATOR = 'global/icube/giftcardaccount/separator';

    const CODE_GENERATION_ATTEMPTS = 1000;

    protected function _construct()
    {
        $this->_init('icube_giftcardaccount/pool');
    }

    public function generatePool()
    {
        $this->cleanupFree();

        $website = Mage::app()->getWebsite($this->getWebsiteId());
        $size = $website->getConfig(self::XML_CONFIG_POOL_SIZE);

        for ($i=0; $i<$size;$i++) {
            $attempt = 0;
            do {
                if ($attempt>=self::CODE_GENERATION_ATTEMPTS) {
                    Mage::throwException(
                        Mage::helper('icube_giftcardaccount')->__('Unable to create full code pool size. Please check settings and try again.')
                    );
                }
                $code = $this->_generateCode();
                $attempt++;
            } while ($this->getResource()->exists($code));

            $this->getResource()->saveCode($code);
        }
        return $this;
    }

    /**
     * Checks pool threshold and call codes generation in case if free codes count is less than threshold value
     *
     * @return Icube_GiftCardAccount_Model_Pool
     */
    public function applyCodesGeneration()
    {
        $website = Mage::app()->getWebsite($this->getWebsiteId());
        $threshold = $website->getConfig(self::XML_CONFIG_POOL_THRESHOLD);
        if ($this->getPoolUsageInfo()->getFree() < $threshold) {
            $this->generatePool();
        }
        return $this;
    }

    /**
     * Generate gift card code
     *
     * @return string
     */
    protected function _generateCode()
    {
        $website = Mage::app()->getWebsite($this->getWebsiteId());

        $format  = $website->getConfig(self::XML_CONFIG_CODE_FORMAT);
        if (!$format) {
            $format = 'alphanum';
        }
        $length  = max(1, (int) $website->getConfig(self::XML_CONFIG_CODE_LENGTH));
        $split   = max(0, (int) $website->getConfig(self::XML_CONFIG_CODE_SPLIT));
        $suffix  = $website->getConfig(self::XML_CONFIG_CODE_SUFFIX);
        $prefix  = $website->getConfig(self::XML_CONFIG_CODE_PREFIX);

        $splitChar = $this->getCodeSeparator();
        $charset = str_split((string) Mage::app()->getConfig()->getNode(sprintf(self::XML_CHARSET_NODE, $format)));

        $code = '';
        for ($i=0; $i<$length; $i++) {
            $char = $charset[array_rand($charset)];
            if ($split > 0 && ($i%$split) == 0 && $i != 0) {
                $char = "{$splitChar}{$char}";
            }
            $code .= $char;
        }

        $code = "{$prefix}{$code}{$suffix}";
        return $code;
    }

    public function getCodeSeparator()
    {
        return (string) Mage::app()->getConfig()->getNode(self::XML_CHARSET_SEPARATOR);
    }
}

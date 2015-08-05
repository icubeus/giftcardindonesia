<?php
/**
 *  Icube_GiftCardAccount_Model_Giftcardaccount model object definition.
 *
 * @method Icube_GiftCardAccount_Model_Resource_Giftcardaccount _getResource()
 * @method Icube_GiftCardAccount_Model_Resource_Giftcardaccount getResource()
 * @method string getCode()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setCode(string $value)
 * @method int getStatus()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setStatus(int $value)
 * @method string getDateCreated()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setDateCreated(string $value)
 * @method string getDateExpires()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setDateExpires(string $value)
 * @method int getWebsiteId()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setWebsiteId(int $value)
 * @method float getBalance()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setBalance(float $value)
 * @method int getState()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setState(int $value)
 * @method int getIsRedeemable()
 * @method Icube_GiftCardAccount_Model_Giftcardaccount setIsRedeemable(int $value)
 *
 * @category    Icube
 * @package     Icube_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Icube_GiftCardAccount_Model_Giftcardaccount extends Mage_Core_Model_Abstract
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * App model
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Status Constant for Gift Card: USED
     *
     * @var integer
     */
    const STATUS_DISABLED = 0;

    /**
     * Status Constant for Gift Card: ENABLED
     *
     * @var integer
     */
    const STATUS_ENABLED = 1;

    /**
     * State Constant for Gift Card: AVAILABLE
     *
     * @var integer
     */
    const STATE_AVAILABLE = 0;

    /**
     * State Constant for Gift Card: USED
     *
     * @var integer
     */
    const STATE_USED = 1;

    /**
     * State Constant for Gift Card: REDEEMED
     *
     * @var integer
     */
    const STATE_REDEEMED = 2;

    /**
     * State Constant for Gift Card: EXPIRED
     *
     * @var integer
     */
    const STATE_EXPIRED = 3;

    /**
     * REDEMABLE gift card constant
     *
     * @var integer
     */
    const REDEEMABLE = 1;

    /**
     * NOT REDEMABLE gift card constant
     *
     * @var integer
     */
    const NOT_REDEEMABLE = 0;

    /**
     * Prefix string for event
     *
     * @var string
     */
    protected $_eventPrefix = 'icube_giftcardaccount';

    /**
     * Event Object
     *
     * @var string
     */
    protected $_eventObject = 'giftcardaccount';
    /**
     * Giftcard code that was requested for load
     *
     * @var bool|string
     */
    protected $_requestedCode = false;

    /**
     * Default Model Name for Gift Card Pool Class
     *
     * @var string
     */
    protected $_defaultPoolModelClass = 'icube_giftcardaccount/pool';

    /**
     * Static variable to contain codes, that were saved on previous steps in series of consecutive saves
     * Used if you use different read and write connections
     *
     * @var array
     */
    protected static $_alreadySelectedIds = array();

    /**
     * Constructor with parameters.
     *
     * Array of arguments with keys:
     *  - 'factory' Mage_Core_Model_Factory
     *  - 'app' Mage_Core_Model_App
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
        unset($args['factory'], $args['app']);
        parent::__construct($args);
        $this->_init('icube_giftcardaccount/giftcardaccount');
    }

    /**
     * This method is the overriden _beforeSave method. It calls the parent's implementation first,
     * then proceeds to perform activities specific to gift card accounts
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        Mage::log('$this->getCode():'.$this->getCode(),null,'GCdata.log',true);
        $this->_parentBeforeSave();
        $this->_preSaveProcess();
    }

    /**
     * Abstracting away the parent's before save method to make the code more unit testable
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _parentBeforeSave()
    {
        return parent::_beforeSave();
    }

    /**
     * This function represents all the gift card account specific pre save activities. This includes
     * 1. Balance validation
     * 2. Expiry date validation
     * 3. Setting data for the new object/setting state of a used gift card
     * 4. History actions
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _preSaveProcess()
    {
        if ($this->getBalance() < 0) {
            throw new Mage_Core_Exception(
                $this->helper('icube_giftcardaccount')->__('Balance cannot be less than zero.')
            );
        }

        $this->_setAndValidateDateExpires();

        if (!$this->getId()) {
            $this->_setDataForNewObject();
        } else {
            $this->_setStateByBalance();
        }

        $this->_setHistoryActionByBalance();

        return $this;
    }

    /**
     * This function performs all activities needed during pre-processing steps of a new gift card.
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _setDataForNewObject()
    {
        $now = $this->_app->getLocale()->date()
            ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
            ->toString(Varien_Date::DATE_INTERNAL_FORMAT);

        $this->setDateCreated($now);
        if (!$this->hasCode()) {
            $this->_defineCode();
        }
        $this->setIsNew(true);

        if (!$this->hasHistoryAction()) {
            $this->setHistoryAction(Icube_GiftCardAccount_Model_History::ACTION_CREATED);
        }
        return $this;
    }

    /**
     * This function's sets the history action of gift cards based on its balance.
     * If the balance of a gift card is not equal to its original value, it's been updated.
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _setHistoryActionByBalance()
    {
        if (!$this->hasHistoryAction() && $this->getOrigData('balance') != $this->getBalance()) {
            $this->setHistoryAction(Icube_GiftCardAccount_Model_History::ACTION_UPDATED)
                ->setBalanceDelta($this->getBalance() - $this->getOrigData('balance'));
        }
        return $this;
    }

    /**
     * This function's sets the the correct gift card state in relation to the current balance
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _setStateByBalance()
    {
        if ($this->getOrigData('balance') != $this->getBalance()) {
            if ($this->getBalance() > 0) {
                $this->setState(self::STATE_AVAILABLE);
            } elseif ($this->getIsRedeemable() && $this->getIsRedeemed()) {
                $this->setState(self::STATE_REDEEMED);
            } else {
                $this->setState(self::STATE_USED);
            }
        }
        return $this;
    }

    /**
     * This function validates and operates on 'date expires' field of a gift card.
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _setAndValidateDateExpires()
    {
        if (is_numeric($this->getLifetime()) && $this->getLifetime() > 0) {
            $this->setDateExpires(date('Y-m-d', strtotime("now +{$this->getLifetime()}days")));
        } else {
            if ($this->getDateExpires()) {
                $expirationDate = $this->_app
                    ->getLocale()->date($this->getDateExpires(),
                        Varien_Date::DATE_INTERNAL_FORMAT, null, false);
                $currentDate = $this->_app
                    ->getLocale()->date(null, Varien_Date::DATE_INTERNAL_FORMAT, null, false);
                if ($expirationDate < $currentDate) {
                    throw new Mage_Core_Exception(
                        $this->helper('icube_giftcardaccount')->__('Expiration date cannot be in the past.')
                    );
                }
            } else {
                $this->setDateExpires(null);
            }
        }
        return $this;
    }

    /**
     * This method abstracts all the object's post-save processing.
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if ($this->getIsNew()) {
            $this->getPoolModel()
                ->setId($this->getCode())
                ->setStatus(Icube_GiftCardAccount_Model_Pool_Abstract::STATUS_USED)
                ->save();

            self::$_alreadySelectedIds[] = $this->getCode();
        }


        $this->_parentAfterSave();
    }

    /**
     * Abstracting away the parent's after save method to make the code more unit testable
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _parentAfterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Generate and save gift card account code
     *
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    protected function _defineCode()
    {
        $code = $this->getPoolModel()->setExcludedIds(self::$_alreadySelectedIds)->shift();
        Mage::log('$code:'.$code,null,'GCdata.log',true);
        return $this->setCode($code);
    }

    /**
     * Load gift card account model using specified code
     *
     * @param string $code
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function loadByCode($code)
    {
        $this->_requestedCode = $code;

        return $this->load($code, 'code');
    }

    /**
     * Add gift card to quote gift card storage
     *
     * @param bool $saveQuote
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function addToCart($saveQuote = true, $quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->_getCheckoutSession()->getQuote();
        }
        $website = Mage::app()->getStore($quote->getStoreId())->getWebsite();
        if ($this->isValid(true, true, $website)) {
            $cards = Mage::helper('icube_giftcardaccount')->getCards($quote);
            if (!$cards) {
                $cards = array();
            } else {
                foreach ($cards as $one) {
                    if ($one['i'] == $this->getId()) {
                        Mage::throwException(
                            Mage::helper('icube_giftcardaccount')->__('This gift card account is already in the quote.')
                        );
                    }
                }
            }
            $cards[] = array(
                // id
                'i' => $this->getId(),
                // code
                'c' => $this->getCode(),
                // amount
                'a' => $this->getBalance(),
                // base amount
                'ba' => $this->getBalance(),
            );
            Mage::helper('icube_giftcardaccount')->setCards($quote, $cards);

            if ($saveQuote) {
                $quote->save();
            }
        }

        return $this;
    }

    /**
     * Remove gift card from quote gift card storage
     *
     * @param bool $saveQuote
     * @param Mage_Sales_Model_Quote|null $quote
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function removeFromCart($saveQuote = true, $quote = null)
    {
        if (!$this->getId()) {
            $this->_throwException(
                Mage::helper('icube_giftcardaccount')->__('Wrong gift card account code: "%s".', $this->_requestedCode)
            );
        }
        if (is_null($quote)) {
            $quote = $this->_getCheckoutSession()->getQuote();
        }

        $cards = Mage::helper('icube_giftcardaccount')->getCards($quote);
        if ($cards) {
            foreach ($cards as $k => $one) {
                if ($one['i'] == $this->getId()) {
                    unset($cards[$k]);
                    Mage::helper('icube_giftcardaccount')->setCards($quote, $cards);

                    if ($saveQuote) {
                        $quote->collectTotals()->save();
                    }
                    return $this;
                }
            }
        }

        $this->_throwException(
            Mage::helper('icube_giftcardaccount')->__('This gift card account wasn\'t found in the quote.')
        );
    }

    /**
     * Return checkout/session model singleton
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Check if this gift card is expired at the moment
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->getDateExpires()) {
            return false;
        }

        $currentDate = strtotime(Mage::getModel('core/date')->date('Y-m-d'));

        if (strtotime($this->getDateExpires()) < $currentDate) {
            return true;
        }
        return false;
    }


    /**
     * Check all the gift card validity attributes
     *
     * @param bool $expirationCheck
     * @param bool $statusCheck
     * @param mixed $websiteCheck
     * @param mixed $balanceCheck
     * @return bool
     */
    public function isValid($expirationCheck = true, $statusCheck = true, $websiteCheck = false, $balanceCheck = true)
    {
        if (!$this->getId()) {
            $this->_throwException(
                Mage::helper('icube_giftcardaccount')->__('Wrong gift card account ID. Requested code: "%s"', $this->_requestedCode)
            );
        }

        if ($websiteCheck) {
            if ($websiteCheck === true) {
                $websiteCheck = null;
            }
            $website = Mage::app()->getWebsite($websiteCheck)->getId();
            if ($this->getWebsiteId() != $website) {
                $this->_throwException(
                    Mage::helper('icube_giftcardaccount')->__('Wrong gift card account website: %s.', $this->getWebsiteId())
                );
            }
        }

        if ($statusCheck && ($this->getStatus() != self::STATUS_ENABLED)) {
            $this->_throwException(
                Mage::helper('icube_giftcardaccount')->__('Gift card account %s is not enabled.', $this->getId())
            );
        }

        if ($expirationCheck && $this->isExpired()) {
            $this->_throwException(
                Mage::helper('icube_giftcardaccount')->__('Gift card account %s is expired.', $this->getId())
            );
        }

        if ($balanceCheck) {
            if ($this->getBalance() <= 0) {
                $this->_throwException(
                    Mage::helper('icube_giftcardaccount')->__('Gift card account %s balance does not have funds.', $this->getId())
                );
            }
            if ($balanceCheck !== true && is_numeric($balanceCheck)) {
                if ($this->getBalance() < $balanceCheck) {
                    $this->_throwException(
                        Mage::helper('icube_giftcardaccount')->__('Gift card account %s balance is less than amount to be charged.', $this->getId())
                    );
                }
            }
        }

        return true;
    }

    /**
     * Reduce Gift Card Account balance by specified amount
     *
     * @param decimal $amount
     */
    public function charge($amount)
    {
        if ($this->isValid(false, false, false, $amount)) {
            $this->setBalanceDelta(-$amount)
                ->setBalance($this->getBalance() - $amount)
                ->setHistoryAction(Icube_GiftCardAccount_Model_History::ACTION_USED);
        }

        return $this;
    }

    /**
     * Revert amount to gift card balance if order was not placed
     *
     * @param   float $amount
     * @return  Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function revert($amount)
    {
        $amount = (float)$amount;

        if ($amount > 0 && $this->isValid(true, true, false, false)) {
            $this->setBalanceDelta($amount)
                ->setBalance($this->getBalance() + $amount)
                ->setHistoryAction(Icube_GiftCardAccount_Model_History::ACTION_UPDATED);
        }

        return $this;
    }

    /**
     * Return Gift Card Account state as user-friendly label
     *
     * @deprecated after 1.3.2.3 use magic method instead
     * @return string
     */
    public function getStateText()
    {
        return $this->_setStateText();
    }

    /**
     * Set state text on after load
     *
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function _afterLoad()
    {
        $this->_setStateText();
        return $this->_parentAfterLoad();
    }

    /**
     * Abstracting away the parent's after load method to make the code more unit testable
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _parentAfterLoad()
    {
        return parent::_afterLoad();
    }


    /**
     * Return Gift Card Account state options
     *
     * @return array
     */
    public function getStatesAsOptionList()
    {
        $result = array();

        $result[self::STATE_AVAILABLE] = Mage::helper('icube_giftcardaccount')->__('Available');
        $result[self::STATE_USED] = Mage::helper('icube_giftcardaccount')->__('Used');
        $result[self::STATE_REDEEMED] = Mage::helper('icube_giftcardaccount')->__('Redeemed');
        $result[self::STATE_EXPIRED] = Mage::helper('icube_giftcardaccount')->__('Expired');

        return $result;
    }

    /**
     * Return code pool model class name
     *
     * @return string
     */
    public function getPoolModelClass()
    {
        if (!$this->hasPoolModelClass()) {
            $this->setPoolModelClass($this->_defaultPoolModelClass);
        }
        return $this->getData('pool_model_class');
    }

    /**
     * Retreive pool model instance
     *
     * @return Icube_GiftCardAccount_Model_Pool_Abstract
     */
    public function getPoolModel()
    {
        return Mage::getModel($this->getPoolModelClass());
    }

    /**
     * Update gift card accounts state
     *
     * @param array $ids
     * @param int $state
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function updateState($ids, $state)
    {
        if ($ids) {
            $this->getResource()->updateState($ids, $state);
        }
        return $this;
    }

    /**
     * Redeem gift card (-gca balance, +cb balance)
     *
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function redeem($customerId = null)
    {
        if ($this->isValid(true, true, true, true)) {
            if ($this->getIsRedeemable() != self::REDEEMABLE) {
                $this->_throwException(sprintf('Gift card account %s is not redeemable.', $this->getId()));
            }
            if (is_null($customerId)) {
                $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            }
            if (!$customerId) {
                Mage::throwException(Mage::helper('icube_giftcardaccount')->__('Invalid customer ID supplied.'));
            }

            $additionalInfo = Mage::helper('icube_giftcardaccount')->__('Gift Card Redeemed: %s. For customer #%s.', $this->getCode(), $customerId);

            $balance = Mage::getModel('icube_customerbalance/balance')
                ->setCustomerId($customerId)
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->setAmountDelta($this->getBalance())
                ->setNotifyByEmail(false)
                ->setUpdatedActionAdditionalInfo($additionalInfo)
                ->save();

            $this->setBalanceDelta(-$this->getBalance())
                ->setHistoryAction(Icube_GiftCardAccount_Model_History::ACTION_REDEEMED)
                ->setBalance(0)
                ->setCustomerId($customerId)
                ->save();
        }

        return $this;
    }

    /**
     * Send an email about the gift card
     *
     * @return Icube_GiftCardAccount_Model_Giftcardaccount
     */
    public function sendEmail()
    {
        $recipientName = $this->getRecipientName();
        $recipientEmail = $this->getRecipientEmail();
        $recipientStore = $this->getRecipientStore();
        if (is_null($recipientStore)) {
            $recipientStore = Mage::app()->getWebsite($this->getWebsiteId())->getDefaultStore();
        } else {
            $recipientStore = Mage::app()->getStore($recipientStore);
        }

        $storeId = $recipientStore->getId();

        $balance = $this->getBalance();
        $code = $this->getCode();

        $balance = Mage::app()->getLocale()->currency($recipientStore->getBaseCurrencyCode())->toCurrency($balance);

        $email = Mage::getModel('core/email_template')->setDesignConfig(array('store' => $storeId));
        $email->sendTransactional(
            Mage::getStoreConfig('giftcard/giftcardaccount_email/template', $storeId),
            Mage::getStoreConfig('giftcard/giftcardaccount_email/identity', $storeId),
            $recipientEmail,
            $recipientName,
            array(
                'name' => $recipientName,
                'code' => $code,
                'balance' => $balance,
                'store' => $recipientStore,
                'store_name' => $recipientStore->getName() // @deprecated after 1.4.0.0-beta1
            )
        );

        $this->setEmailSent(false);
        if ($email->getSentSuccess()) {
            $this->setEmailSent(true)
                ->setHistoryAction(Icube_GiftCardAccount_Model_History::ACTION_SENT)
                ->save();
        }
    }

    /**
     * Set state text by loaded state code
     * Used in _afterLoad() and old getStateText()
     *
     * @return string
     */
    protected function _setStateText()
    {
        $states = $this->getStatesAsOptionList();

        if (isset($states[$this->getState()])) {
            $stateText = $states[$this->getState()];
            $this->setStateText($stateText);
            return $stateText;
        }
        return '';
    }

    /**
     * Obscure real exception message to prevent brute force attacks
     *
     * @throws Mage_Core_Exception
     * @param string $realMessage
     * @param string $fakeMessage
     */
    protected function _throwException($realMessage, $fakeMessage = '')
    {
        $e = Mage::exception('Mage_Core', $realMessage);
        Mage::logException($e);
        if (!$fakeMessage) {
            $fakeMessage = Mage::helper('icube_giftcardaccount')->__('Wrong gift card code.');
        }
        $e->setMessage($fakeMessage);
        throw $e;
    }

    /**
     * Retrieves helper class based on its name
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    public function helper($name)
    {
        return $this->_factory->getHelper($name);
    }
}

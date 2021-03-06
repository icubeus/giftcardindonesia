<?php
class Icube_GiftCardAccount_CartController extends Mage_Core_Controller_Front_Action
{
    /**
     * No index action, forward to 404
     *
     */
    public function indexAction()
    {
        $this->_forward('noRoute');
    }

    /**
     * Add Gift Card to current quote
     *
     */
    public function addAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (strlen($code) > Icube_GiftCardAccount_Helper_Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    Mage::throwException(Mage::helper('icube_giftcardaccount')->__('Wrong gift card code.'));
                }
                Mage::getModel('icube_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Card "%s" was added.', Mage::helper('core')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::dispatchEvent('icube_giftcardaccount_add', array('status' => 'fail', 'code' => $code));
                Mage::getSingleton('checkout/session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot apply gift card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }

    public function removeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            try {
                Mage::getModel('icube_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Card "%s" was removed.', Mage::helper('core')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot remove gift card.'));
            }
            $this->_redirect('checkout/cart');
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Check a gift card account availability
     *
     */
    public function checkAction()
    {
        return $this->quickCheckAction();
    }

    /**
     * Check a gift card account availability
     *
     */
    public function quickCheckAction()
    {
        /* @var $card Icube_GiftCardAccount_Model_Giftcardaccount */
        $card = Mage::getModel('icube_giftcardaccount/giftcardaccount')
            ->loadByCode($this->getRequest()->getParam('giftcard_code', ''));
        Mage::register('current_giftcardaccount', $card);
        try {
            $card->isValid(true, true, true, false);
        }
        catch (Mage_Core_Exception $e) {
            $card->unsetData();
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Redeem gift card
     *
     */
    public function redeemAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (!Mage::helper('icube_customerbalance')->isEnabled()) {
                    Mage::throwException($this->__('Redemption functionality is disabled.'));
                }
                $model = Mage::getModel('icube_giftcardaccount/giftcardaccount')->loadByCode($code)
                    ->setIsRedeemed(true)->redeem();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Card "%s" was redeemed.', Mage::helper('core')->escapeHtml($code))
                );

                $amount = $model->getOrigData('balance');
                $distributionId = $model->getData('distribution_id');
                $gc = Mage::helper('icube_giftcard/api_data')->redeem($distributionId,$amount);
                if(!is_null($gc->cardNo)) {
                    $model->setData('balance',$gc->balance);
                    $model->save();
                } else {
                    if(is_null($gc->message)) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('icube_giftcardaccount')->__('Redeem failed.'));
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError($gc->message);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot redeem Gift Card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }
}

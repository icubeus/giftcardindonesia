<?php
class Icube_GiftCardAccount_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Redeem gift card
     *
     */
    public function indexAction()
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
                Mage::getSingleton('customer/session')->addSuccess(
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
                    $this->_redirect('*/*/*');
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addException($e, $this->__('Cannot redeem Gift Card.'));
            }
            $this->_redirect('*/*/*');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Gift Card'));
        }
        $this->renderLayout();
    }
}

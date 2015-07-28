<?php
/**
 * Customerbalance controller for My Account
 *
 */
class Icube_CustomerBalance_InfoController extends Mage_Core_Controller_Front_Action
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
     * Store Credit dashboard
     *
     */
    public function indexAction()
    {
        if (!Mage::helper('icube_customerbalance')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('icube_customerbalance')->__('Store Credit'));
        }
        $this->renderLayout();
    }
}

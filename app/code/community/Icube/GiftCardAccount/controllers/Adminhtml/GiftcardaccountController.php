<?php
class Icube_GiftCardAccount_Adminhtml_GiftcardaccountController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Defines if status message of code pool is show
     *
     * @var bool
     */
    protected $_showCodePoolStatusMessage = true;

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Gift Card Accounts'));

        if ($this->_showCodePoolStatusMessage) {
            $usage = Mage::getModel('icube_giftcardaccount/pool')->getPoolUsageInfo();

            $function = 'addNotice';
            if ($usage->getPercent() == 100) {
                $function = 'addError';
            }

            $url = Mage::getSingleton('adminhtml/url')->getUrl('*/*/generate');
            Mage::getSingleton('adminhtml/session')->$function(
                Mage::helper('icube_giftcardaccount')->__('Code Pool used: <b>%.2f%%</b> (free <b>%d</b> of <b>%d</b> total). Generate new code pool <a href="%s">here</a>.', $usage->getPercent(), $usage->getFree(), $usage->getTotal(), $url)
            );
        }

        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcardaccount');
        $this->renderLayout();
    }


    /**
     * Create new Gift Card Account
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit GiftCardAccount
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initGca();

        if (!$model->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('icube_giftcardaccount')->__('This Gift Card Account no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getCode() : $this->__('New Account'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $label = $id ? Mage::helper('icube_giftcardaccount')->__('Edit Gift Card Account')
            : Mage::helper('icube_giftcardaccount')->__('New Gift Card Account');
        $this->loadLayout()
            ->_addBreadcrumb($label, $label)
            ->_addContent(
                $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_edit')
                    ->setData('form_action_url', $this->getUrl('*/*/save'))
            )
            ->_addLeft(
                $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_edit_tabs')
            )
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $id = $this->getRequest()->getParam('giftcardaccount_id');
            $model = $this->_initGca('giftcardaccount_id');
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('icube_giftcardaccount')->__('This Gift Card Account no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }

            if (!empty($data)) {
                $model->addData($data);
                Mage::log('data:'.print_r($data,true),null,'GCdata.log',true);
                Mage::log('balance orig:'.$model->getOrigData('balance'),null,'GCdata.log',true);
                if($data[giftcardaccount_id]) { // edit - topup
                        $distributionId = $model->getData('distribution_id');//'00000000000990056817133197574717';
                    $amount = $data[balance] - $model->getOrigData('balance');
                    Mage::log('$amount:'.$amount,null,'GCdata.log',true);
                    if($amount >= 0) {
                        $gc = Mage::helper('icube_giftcard/api_data')->topup($distributionId,$amount);
                        Mage::log('$gc:'.print_r($gc,true),null,'GCdata.log',true);
                        if(!is_null($gc->cardNo)) {
                            $model->setData('balance',$gc->balance);
                        } else {
                            if(is_null($gc->message)) {
                                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('icube_giftcardaccount')->__('No distribution ID available.'));
                            } else {
                                Mage::getSingleton('adminhtml/session')->addError($gc->message);
                            }
                            $this->_redirect('*/*/edit', array('id' => $model->getId()));
                            return;
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('icube_giftcardaccount')->__('Balance value must equal or greater than current balance.'));
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                } else { // create new
                    $distributionId = $data[distribution_id];
                    Mage::log('$distributionId:'.$distributionId,null,'GCdata.log',true);
                    $gc = Mage::helper('icube_giftcard/api_data')->activate($distributionId,$data[balance]);
                    if(!is_null($gc->cardNo)) {
                        Mage::log('$gc:'.print_r($gc,true),null,'GCdata.log',true);
                        $model->setCode($gc->cardNo);
                        $model->setData('distribution_id',$distributionId);
                        $date   = DateTime::createFromFormat('d/m/Y', $gc->expired)->format('Y-m-d');
                        $model->setData('date_expires',$date);
                        Mage::log('$date:'.$date,null,'GCdata.log',true);
                    } else {
                        if(is_null($gc->message)) {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('icube_giftcardaccount')->__('No distribution ID available.'));
                        } else {
                            Mage::getSingleton('adminhtml/session')->addError($gc->message);
                        }
                        $this->_redirect('*/*/edit');
                        return;
                    }
                }
                /*
                 * string(371) "{"cardNo":"6817133197574717","balance":30000,"expired":"03/09/2015","status":"active","approvalCode":788590,"merchant":"Giftcard4Dummies","trxNo":"307382612489163","trxType":"activation","trxCode":"6934657e57a072d4dacc2ad074d4edba1cde8cd7ab707508ab47030cf7b27b542d7954de9f1d2e8b","trxStatus":"accepted","trxAmount":30000,"trxTime":"04/08/2015","terminalId":166,"id":5469}"
                 * data:
                Array(
                    [form_key] => MmGFO4NjMSz3nyMN
                    [status] => 1
                    [is_redeemable] => 1
                    [website_id] => 1
                    [balance] => 15000
                    [date_expires] =>
                    [recipient_email] =>
                    [recipient_name] =>
                    [recipient_store] => 1
                    [send_action] => 0
                )
                */
                //call API here
            }

            // try to save it
            try {
                // save the data
                $model->save();
                $sending = null;
                $status = null;

                if ($model->getSendAction()) {
                    try {
                        if($model->getStatus()){
                            $model->sendEmail();
                            $sending = $model->getEmailSent();
                        }
                        else {
                            $status = true;
                        }
                    } catch (Exception $e) {
                        $sending = false;
                    }
                }

                if (!is_null($sending)) {
                    if ($sending) {
                        Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('icube_giftcardaccount')->__('The gift card account has been saved and sent.')
                        );
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('icube_giftcardaccount')->__('The gift card account has been saved, but email was not sent.')
                        );
                    }
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('icube_giftcardaccount')->__('The gift card account has been saved.')
                    );

                    if ($status) {
                        Mage::getSingleton('adminhtml/session')->addNotice(
                            Mage::helper('icube_giftcardaccount')->__('Email was not sent because the gift card account is not active.')
                        );
                    }
                }

                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('icube_giftcardaccount/giftcardaccount');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('icube_giftcardaccount')->__('Gift Card Account has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('icube_giftcardaccount')->__('Unable to find a Gift Card Account to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Render GCA grid
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'icube_giftcardaccount/adminhtml_giftcardaccount_grid',
                'giftcardaccount.grid'
            )
            ->toHtml()
        );
    }

    /**
     * Generate code pool
     */
    public function generateAction()
    {
        try {
            Mage::getModel('icube_giftcardaccount/pool')->generatePool();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('icube_giftcardaccount')->__('New code pool was generated.')
            );
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addException(
                $e, Mage::helper('icube_giftcardaccount')->__('Unable to generate new code pool.')
            );
        }
        $this->_redirectReferer('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/giftcardaccount');
    }

    /**
     * Render GCA history grid
     */
    public function gridHistoryAction()
    {
        $model = $this->_initGca();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id && !$model->getId()) {
            return;
        }

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_edit_tab_history')
                ->toHtml()
        );
    }

    /**
     * Load GCA from request
     *
     * @param string $idFieldName
     */
    protected function _initGca($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Gift Card Accounts'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('icube_giftcardaccount/giftcardaccount');
        if ($id) {
            $model->load($id);
        }
        Mage::register('current_giftcardaccount', $model);
        return $model;
    }

    /**
     * Export GCA grid to MSXML
     */
    public function exportMsxmlAction()
    {
        $this->_prepareDownloadResponse('giftcardaccounts.xml',
            $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_grid')
                ->getExcelFile($this->__('Gift Card Accounts'))
        );
    }

    /**
     * Export GCA grid to CSV
     */
    public function exportCsvAction()
    {
        $this->_prepareDownloadResponse('giftcardaccounts.csv',
            $this->getLayout()->createBlock('icube_giftcardaccount/adminhtml_giftcardaccount_grid')->getCsvFile()
        );
    }

    /**
     * Delete gift card accounts specified using grid massaction
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('giftcardaccount');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select gift card account(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('icube_giftcardaccount/giftcardaccount')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('date_expires'));

        return $data;
    }

    /**
     * Setter for code pool status message flag
     *
     * @param bool $isShow
     */
    public function setShowCodePoolStatusMessage($isShow)
    {
        $this->_showCodePoolStatusMessage = (bool)$isShow;
    }
}

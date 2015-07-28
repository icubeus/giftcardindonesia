<?php
class Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardaccountGrid');
        $this->setDefaultSort('giftcardaccount_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('giftcardaccount_filter');
    }

    /**
     * Get store from request
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Icube_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getResourceModel('icube_giftcardaccount/giftcardaccount_collection');

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Define grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('giftcardaccount_id',
            array(
                'header'=> Mage::helper('icube_giftcardaccount')->__('ID'),
                'width' => 30,
                'type'  => 'number',
                'index' => 'giftcardaccount_id',
        ));

        $this->addColumn('code',
            array(
                'header'=> Mage::helper('icube_giftcardaccount')->__('Code'),
                'index' => 'code',
        ));

        $this->addColumn('website',
            array(
                'header'    => Mage::helper('icube_giftcardaccount')->__('Website'),
                'width'     => 100,
                'index'     => 'website_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
        ));

        $this->addColumn('date_created',
            array(
                'header'=> Mage::helper('icube_giftcardaccount')->__('Date Created'),
                'width' => 120,
                'type'  => 'date',
                'index' => 'date_created',
        ));

        $this->addColumn('date_expires',
            array(
                'header'  => Mage::helper('icube_giftcardaccount')->__('Expiration Date'),
                'width'   => 120,
                'type'    => 'date',
                'index'   => 'date_expires',
                'default' => '--',
        ));

        $this->addColumn('status',
            array(
                'header'    => Mage::helper('icube_giftcardaccount')->__('Active'),
                'width'     => 50,
                'align'     => 'center',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                    Icube_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                        Mage::helper('icube_giftcardaccount')->__('Yes'),
                    Icube_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                        Mage::helper('icube_giftcardaccount')->__('No'),
                ),
        ));

        $this->addColumn('state',
            array(
                'header'    => Mage::helper('icube_giftcardaccount')->__('Status'),
                'width'     => 100,
                'align'     => 'center',
                'index'     => 'state',
                'type'      => 'options',
                'options'   => Mage::getModel('icube_giftcardaccount/giftcardaccount')->getStatesAsOptionList(),
        ));

        $this->addColumn('balance',
            array(
                'header'        => Mage::helper('icube_giftcardaccount')->__('Balance'),
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                'type'          => 'number',
                'renderer'      => 'icube_giftcardaccount/adminhtml_widget_grid_column_renderer_currency',
                'index'         => 'balance',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action options for this grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('giftcardaccount_id');
        $this->getMassactionBlock()->setFormFieldName('giftcardaccount');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('icube_giftcardaccount')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('icube_giftcardaccount')->__('Are you sure you want to delete these gift card accounts?')
        ));

        return $this;
    }


    /**
     * Define row click callback
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Retrieve row url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'    => $row->getId()
        ));
    }

    /**
     * Invoke export features for grid
     */
    protected function _prepareGrid()
    {
        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportMsxml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareGrid();
    }
}

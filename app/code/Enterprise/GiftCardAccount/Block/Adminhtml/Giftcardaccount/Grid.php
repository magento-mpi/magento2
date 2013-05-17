<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    protected function _construct()
    {
        parent::_construct();
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
     * @return Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getResourceModel('Enterprise_GiftCardAccount_Model_Resource_Giftcardaccount_Collection');

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
                'header'=> Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('ID'),
                'width' => 30,
                'type'  => 'number',
                'index' => 'giftcardaccount_id',
        ));

        $this->addColumn('code',
            array(
                'header'=> Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Code'),
                'index' => 'code',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website',
                array(
                    'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Website'),
                    'width'     => 100,
                    'index'     => 'website_id',
                    'type'      => 'options',
                    'options'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash(),
            ));
        }

        $this->addColumn('date_created',
            array(
                'header'=> Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Created'),
                'width' => 120,
                'type'  => 'date',
                'index' => 'date_created',
        ));

        $this->addColumn('date_expires',
            array(
                'header'  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('End on'),
                'width'   => 120,
                'type'    => 'date',
                'index'   => 'date_expires',
                'default' => '--',
        ));

        $this->addColumn('status',
            array(
                'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Active'),
                'width'     => 50,
                'align'     => 'center',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                    Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                        Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Yes'),
                    Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                        Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('No'),
                ),
        ));

        $states = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')->getStatesAsOptionList();
        $this->addColumn('state',
            array(
                'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Status'),
                'width'     => 100,
                'align'     => 'center',
                'index'     => 'state',
                'type'      => 'options',
                'options'   => $states,
        ));

        $this->addColumn('balance',
            array(
                'header'        => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance'),
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                'type'          => 'number',
                'renderer'      => 'Enterprise_GiftCardAccount_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency',
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
             'label'=> Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Are you sure you want to delete these gift card accounts?')
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
        $this->addExportType('*/*/exportCsv', Mage::helper('Mage_Customer_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportMsxml', Mage::helper('Mage_Customer_Helper_Data')->__('Excel XML'));
        return parent::_prepareGrid();
    }
}

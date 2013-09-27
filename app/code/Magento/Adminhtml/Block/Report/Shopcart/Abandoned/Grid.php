<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml abandoned shopping carts report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Shopcart_Abandoned_Grid extends Magento_Adminhtml_Block_Report_Grid_Shopcart
{
    /**
     * @var Magento_Reports_Model_Resource_Quote_CollectionFactory
     */
    protected $_quotesFactory;

    /**
     * @param Magento_Reports_Model_Resource_Quote_CollectionFactory $quotesFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Reports_Model_Resource_Quote_CollectionFactory $quotesFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_quotesFactory = $quotesFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridAbandoned');
    }

    protected function _prepareCollection()
    {
        /** @var $collection Magento_Reports_Model_Resource_Quote_Collection */
        $collection = $this->_quotesFactory->create();

        $filter = $this->getParam($this->getVarNameFilter(), array());
        if ($filter) {
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
        }

        if (!empty($data)) {
            $collection->prepareForAbandonedReport($this->_storeIds, $data);
        } else {
            $collection->prepareForAbandonedReport($this->_storeIds);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
        $skip = array('subtotal', 'customer_name', 'email'/*, 'created_at', 'updated_at'*/);

        if (in_array($field, $skip)) {
            return $this;
        }

        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_name', array(
            'header'    => __('Customer'),
            'index'     => 'customer_name',
            'sortable'  => false,
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('email', array(
            'header'    => __('Email'),
            'index'     => 'email',
            'sortable'  => false,
            'header_css_class'  => 'col-email',
            'column_css_class'  => 'col-email'
        ));

        $this->addColumn('items_count', array(
            'header'    => __('Products'),
            'index'     => 'items_count',
            'sortable'  => false,
            'type'      => 'number',
            'header_css_class'  => 'col-number',
            'column_css_class'  => 'col-number'
        ));

        $this->addColumn('items_qty', array(
            'header'    => __('Quantity'),
            'index'     => 'items_qty',
            'sortable'  => false,
            'type'      => 'number',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        if ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } else if ($this->getRequest()->getParam('store')) {
            $storeIds = array((int)$this->getRequest()->getParam('store'));
        } else {
            $storeIds = array();
        }
        $this->setStoreIds($storeIds);
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('subtotal', array(
            'header'        => __('Subtotal'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'subtotal',
            'sortable'      => false,
            'renderer'      => 'Magento_Adminhtml_Block_Report_Grid_Column_Renderer_Currency',
            'rate'          => $this->getRate($currencyCode),
            'header_css_class'  => 'col-subtotal',
            'column_css_class'  => 'col-subtotal'
        ));

        $this->addColumn('coupon_code', array(
            'header'    => __('Applied Coupon'),
            'index'     => 'coupon_code',
            'sortable'  => false,
            'header_css_class'  => 'col-coupon',
            'column_css_class'  => 'col-coupon'
        ));

        $this->addColumn('created_at', array(
            'header'    => __('Created'),
            'type'      => 'datetime',
            'index'     => 'created_at',
            'filter_index'=> 'main_table.created_at',
            'sortable'  => false,
            'header_css_class'  => 'col-created',
            'column_css_class'  => 'col-created'
        ));

        $this->addColumn('updated_at', array(
            'header'    => __('Updated'),
            'type'      => 'datetime',
            'index'     => 'updated_at',
            'filter_index'=> 'main_table.updated_at',
            'sortable'  => false,
            'header_css_class'  => 'col-updated',
            'column_css_class'  => 'col-updated'
        ));

        $this->addColumn('remote_ip', array(
            'header'    => __('IP Address'),
            'index'     => 'remote_ip',
            'sortable'  => false,
            'header_css_class'  => 'col-ip',
            'column_css_class'  => 'col-ip'
        ));

        $this->addExportType('*/*/exportAbandonedCsv', __('CSV'));
        $this->addExportType('*/*/exportAbandonedExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id'=>$row->getCustomerId(), 'active_tab'=>'cart'));
    }
}

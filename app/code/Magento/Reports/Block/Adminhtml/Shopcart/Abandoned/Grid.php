<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml abandoned shopping carts report grid block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Shopcart\Abandoned;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\Shopcart
{
    /**
     * @var \Magento\Reports\Model\Resource\Quote\CollectionFactory
     */
    protected $_quotesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Reports\Model\Resource\Quote\CollectionFactory $quotesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Reports\Model\Resource\Quote\CollectionFactory $quotesFactory,
        array $data = array()
    ) {
        $this->_quotesFactory = $quotesFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridAbandoned');
    }

    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Reports\Model\Resource\Quote\Collection */
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
            'renderer'      => 'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency',
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
        return $this->getUrl('customer/index/edit', array('id'=>$row->getCustomerId(), 'active_tab'=>'cart'));
    }
}

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
 * Adminhtml products in carts report grid block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Shopcart\Product;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\Shopcart
{
    /**
     * @var \Magento\Reports\Model\Resource\Quote\CollectionFactory
     */
    protected $_quotesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Reports\Model\Resource\Quote\CollectionFactory $quotesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Reports\Model\Resource\Quote\CollectionFactory $quotesFactory,
        array $data = array()
    ) {
        $this->_quotesFactory = $quotesFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Reports\Model\Resource\Quote\Collection */
        $collection = $this->_quotesFactory->create();
        $collection->prepareForProductsInCarts()
            ->setSelectCountSqlType(\Magento\Reports\Model\Resource\Quote\Collection::SELECT_COUNT_SQL_TYPE_CART);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'align'     =>'right',
            'index'     =>'entity_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('name', array(
            'header'    =>__('Product'),
            'index'     =>'name',
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('price', array(
            'header'    =>__('Price'),
            'type'      =>'currency',
            'currency_code' => $currencyCode,
            'index'     =>'price',
            'renderer'  =>'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency',
            'rate'          => $this->getRate($currencyCode),
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addColumn('carts', array(
            'header'    =>__('Carts'),
            'align'     =>'right',
            'index'     =>'carts',
            'header_css_class'  => 'col-carts',
            'column_css_class'  => 'col-carts'
        ));

        $this->addColumn('orders', array(
            'header'    =>__('Orders'),
            'align'     =>'right',
            'index'     =>'orders',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', __('CSV'));
        $this->addExportType('*/*/exportProductExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', array('id'=>$row->getEntityId()));
    }
}


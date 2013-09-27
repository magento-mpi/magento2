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
 * Adminhtml products in carts report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Shopcart_Product_Grid extends Magento_Adminhtml_Block_Report_Grid_Shopcart
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
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {
        /** @var $collection Magento_Reports_Model_Resource_Quote_Collection */
        $collection = $this->_quotesFactory->create();
        $collection->prepareForProductsInCarts()
            ->setSelectCountSqlType(Magento_Reports_Model_Resource_Quote_Collection::SELECT_COUNT_SQL_TYPE_CART);
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
            'renderer'  =>'Magento_Adminhtml_Block_Report_Grid_Column_Renderer_Currency',
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
        return $this->getUrl('*/catalog_product/edit', array('id'=>$row->getEntityId()));
    }
}


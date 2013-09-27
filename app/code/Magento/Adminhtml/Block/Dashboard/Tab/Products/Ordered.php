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
 * Adminhtml dashboard most ordered products grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Tab_Products_Ordered extends Magento_Adminhtml_Block_Dashboard_Grid
{
    /**
     * @var Magento_Sales_Model_Resource_Report_Bestsellers_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Sales_Model_Resource_Report_Bestsellers_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Model_Resource_Report_Bestsellers_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsOrderedGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->_coreData->isModuleEnabled('Magento_Sales')) {
            return $this;
        }
        if ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
        }

        $collection = $this->_collectionFactory->create()
            ->setModel('Magento_Catalog_Model_Product')
            ->addStoreFilter($storeId)
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'sortable'  => false,
            'index'     => 'product_name'
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'width'     => '120px',
            'type'      => 'currency',
            'currency_code' => (string) $this->_storeManager->getStore((int)$this->getParam('store'))
                ->getBaseCurrencyCode(),
            'sortable'  => false,
            'index'     => 'product_price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => __('Order Quantity'),
            'width'     => '120px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'qty_ordered',
            'type'      => 'number'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /*
     * Returns row url to show in admin dashboard
     * $row is bestseller row wrapped in Product model
     *
     * @param Magento_Catalog_Model_Product $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        // getId() would return id of bestseller row, and product id we get by getProductId()
        $productId = $row->getProductId();

        // No url is possible for non-existing products
        if (!$productId) {
            return '';
        }

        $params = array('id' => $productId);
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        return $this->getUrl('*/catalog_product/edit', $params);
    }
}

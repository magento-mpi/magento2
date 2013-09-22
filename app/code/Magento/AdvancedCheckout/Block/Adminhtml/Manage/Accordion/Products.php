<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for catalog salable products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class Products
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
{
    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Config $coreConfig,
        Magento_Catalog_Model_ProductFactory $productFactory,
        array $data = array()
    ) {
        parent::__construct(
            $coreData,
            $context,
            $storeManager,
            $urlModel,
            $coreRegistry,
            $data
        );
        $this->_coreConfig = $coreConfig;
        $this->_productFactory = $productFactory;
    }

    /**
     * Block initializing, grid parameters
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_products');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setHeaderText(__('Products'));
    }

    /**
     * Return custom object name for js grid
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return 'productsGrid';
    }

    /**
     * Return items collection
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = \Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
            $collection = $this->_productFactory->create()->getCollection()
                ->setStore($this->_getStore())
                ->addAttributeToSelect($attributes)
                ->addAttributeToSelect('sku')
                ->addAttributeToFilter(
                    'type_id',
                    array_keys(
                        $this->_coreConfig->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
                    )
                )->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
                ->addStoreFilter($this->_getStore());
            \Mage::getSingleton('Magento\CatalogInventory\Model\Stock\Status')->addIsInStockFilterToCollection($collection);
            $this->setData('items_collection', $collection);
        }
        return $this->getData('items_collection');
    }

    /**
     * Prepare Grid columns
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'renderer'  => 'Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer\Product',
            'index'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'type'      => 'currency',
            'column_css_class' => 'price',
            'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
            'rate' => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer\Price'
        ));

        $this->_addControlColumns();

        return $this;
    }

    /**
     * Custom products grid search callback
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getChildBlock('search_button')->setOnclick('checkoutObj.searchProducts()');
        return $this;
    }

    /**
     * Search by selected products
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (!$productIds) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Return array of selected product ids from request
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        if ($this->getRequest()->getPost('source')) {
            $source = $this->_coreData->jsonDecode($this->getRequest()->getPost('source'));
            if (isset($source['source_products']) && is_array($source['source_products'])) {
                return array_keys($source['source_products']);
            }
        }
        return false;
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/products', array('_current'=>true));
    }

    /**
     * Add columns with controls to manage added products and their quantity
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();
        $this->getColumn('in_products')->setHeader(" ");
    }

    /*
     * Add custom options to product collection
     *
     * return \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Products
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }
}

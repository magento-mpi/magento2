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
 * Adminhtml sales order create search products block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create\Search;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->_coreConfig = $coreConfig;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_search_grid');
        $this->setRowClickCallback('order.productGridRowClick.bind(order)');
        $this->setCheckboxCheckCallback('order.productGridCheckboxCheck.bind(order)');
        $this->setRowInitCallback('order.productGridRowInit.bind(order)');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote')->getStore();
    }

    /**
     * Retrieve quote object
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote')->getQuote();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid
     */
    protected function _prepareCollection()
    {
        $attributes = \Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes();
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection();
        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array_keys(
                $this->_coreConfig->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
            ))
            ->addAttributeToSelect('gift_message_available');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid
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
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer\Price',
        ));

        $this->addColumn('in_products', array(
            'header'    => __('Select'),
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'sortable'  => false,
        ));

        $this->addColumn('qty', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => __('Quantity'),
            'renderer'  => 'Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer\Qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'center',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadBlock', array('block'=>'search_grid', '_current' => true, 'collapse' => null));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', array());

        return $products;
    }

    /*
     * Add custom options to product collection
     *
     * return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _afterLoadCollection() {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Catalog\Model\Product;

/**
 * Adminhtml customer orders grid block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Cart extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Data\CollectionFactory
     */
    protected $_dataCollectionFactory;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Data\CollectionFactory $dataCollectionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_dataCollectionFactory = $dataCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_quoteFactory = $quoteFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->_parentTemplate = $this->getTemplate();
        $this->setTemplate('tab/cart.phtml');
    }

    /**
     * Prepare grid
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->setId('customer_cart_grid' . $this->getWebsiteId());
        parent::_prepareGrid();
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        $storeIds = $this->_storeManager->getWebsite($this->getWebsiteId())->getStoreIds();

        $quote = $this->_quoteFactory->create()
            ->setSharedStoreIds($storeIds)
            ->loadByCustomer($customer);

        if ($quote) {
            $collection = $quote->getItemsCollection(false);
        } else {
            $collection = $this->_dataCollectionFactory->create();
        }

        $collection->addFieldToFilter('parent_item_id', array('null' => true));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => __('ID'),
            'index'     => 'product_id',
            'width'     => '100px',
        ));

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'index'     => 'name',
            'renderer'  => 'Magento\Customer\Block\Adminhtml\Edit\Tab\View\Grid\Renderer\Item'
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'index'     => 'sku',
            'width'     => '100px',
        ));

        $this->addColumn('qty', array(
            'header'    => __('Quantity'),
            'index'     => 'qty',
            'type'      => 'number',
            'width'     => '60px',
        ));

        $this->addColumn('price', array(
            'header'        => __('Price'),
            'index'         => 'price',
            'type'          => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('total', array(
            'header'        => __('Total'),
            'index'         => 'row_total',
            'type'          => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('action', array(
            'header'    => __('Action'),
            'index'     => 'quote_item_id',
            'renderer'  => 'Magento\Customer\Block\Adminhtml\Grid\Renderer\Multiaction',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption'           => __('Configure'),
                    'url'               => 'javascript:void(0)',
                    'process'           => 'configurable',
                    'control_object'    => $this->getJsObjectName() . 'cartControl'
                ),
                array(
                    'caption'   => __('Delete'),
                    'url'       => '#',
                    'onclick'   => 'return ' . $this->getJsObjectName() . 'cartControl.removeItem($item_id);'
                )
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Gets customer assigned to this block
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_coreRegistry->registry('current_customer');
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customer/*/cart', array('_current'=>true, 'website_id' => $this->getWebsiteId()));
    }

    /**
     * @return string
     */
    public function getGridParentHtml()
    {
        $templateName = $this->_viewFileSystem->getFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    /**
     * @param Product|\Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', array('id' => $row->getProductId()));
    }
}

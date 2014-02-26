<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Catalog\Model\Product;

/**
 * Adminhtml customer cart items grid block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Cart extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
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
     * @param \Magento\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Registry $coreRegistry,
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
        $this->setId('customer_view_cart_grid');
        $this->setDefaultSort('added_at', 'desc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(__('There are no items in customer\'s shopping cart at the moment'));
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $quote = $this->_quoteFactory->create();
        // set website to quote, if any
        if ($this->getWebsiteId()) {
            $quote->setWebsite($this->_storeManager->getWebsite($this->getWebsiteId()));
        }

        /** @var \Magento\Customer\Model\Customer $currentCustomer */
        $currentCustomer = $this->_coreRegistry->registry('current_customer');
        if (!is_null($currentCustomer)) {
            $quote->loadByCustomer($currentCustomer->getId());
        }

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
            'header' => __('ID'),
            'index' => 'product_id',
            'width' => '100px',
        ));

        $this->addColumn('name', array(
            'header' => __('Product'),
            'index' => 'name',
        ));

        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'index' => 'sku',
            'width' => '100px',
        ));

        $this->addColumn('qty', array(
            'header' => __('Qty'),
            'index' => 'qty',
            'type'  => 'number',
            'width' => '60px',
        ));

        $this->addColumn('price', array(
            'header' => __('Price'),
            'index' => 'price',
            'type'  => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('total', array(
            'header' => __('Total'),
            'index' => 'row_total',
            'type'  => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param Product|\Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', array('id' => $row->getProductId()));
    }

    /**
     * @return bool
     */
    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() >= 0);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Directory\Model\Currency;

/**
 * Adminhtml customer cart items grid block
 *
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
     * Constructor
     *
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
     * {@inheritdoc}
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
     * Prepare the cart collection.
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $quote = $this->_quoteFactory->create();
        // set website to quote, if any
        if ($this->getWebsiteId()) {
            $quote->setWebsite($this->_storeManager->getWebsite($this->getWebsiteId()));
        }
        $quote->loadByCustomer($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));

        if ($quote) {
            $collection = $quote->getItemsCollection(false);
        } else {
            $collection = $this->_dataCollectionFactory->create();
        }

        $collection->addFieldToFilter('parent_item_id', ['null' => true]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', [
            'header' => __('ID'),
            'index' => 'product_id',
            'width' => '100px',
        ]);

        $this->addColumn('name', [
            'header' => __('Product'),
            'index' => 'name',
        ]);

        $this->addColumn('sku', [
            'header' => __('SKU'),
            'index' => 'sku',
            'width' => '100px',
        ]);

        $this->addColumn('qty', [
            'header' => __('Qty'),
            'index' => 'qty',
            'type'  => 'number',
            'width' => '60px',
        ]);

        $this->addColumn('price', [
            'header' => __('Price'),
            'index' => 'price',
            'type'  => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(Currency::XML_PATH_CURRENCY_BASE),
        ]);

        $this->addColumn('total', [
            'header' => __('Total'),
            'index' => 'row_total',
            'type'  => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(Currency::XML_PATH_CURRENCY_BASE),
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $row->getProductId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() >= 0);
    }
}

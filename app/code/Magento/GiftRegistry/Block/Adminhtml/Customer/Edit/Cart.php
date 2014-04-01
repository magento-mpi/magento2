<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

/**
 * Adminhtml customer cart items grid block
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Cart extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $salesQuoteFactory;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Data\CollectionFactory
     */
    protected $_dataFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Data\CollectionFactory $dataFactory
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Model\QuoteFactory $salesQuoteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Data\CollectionFactory $dataFactory,
        \Magento\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\QuoteFactory $salesQuoteFactory,
        array $data = array()
    ) {
        $this->_dataFactory = $dataFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->customerFactory = $customerFactory;
        $this->salesQuoteFactory = $salesQuoteFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftregistry_customer_cart_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $quote = $this->salesQuoteFactory->create();
        $quote->setWebsite($this->_storeManager->getWebsite($this->getEntity()->getWebsiteId()));
        $quote->loadByCustomer($this->getEntity()->getCustomerId());

        $collection = $quote ? $quote->getItemsCollection(false) : $this->_dataFactory->create();
        $collection->addFieldToFilter('parent_item_id', array('null' => true));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_id',
            array('header' => __('Product ID'), 'index' => 'product_id', 'type' => 'number', 'width' => '100px')
        );

        $this->addColumn('name', array('header' => __('Product'), 'index' => 'name'));

        $this->addColumn('sku', array('header' => __('SKU'), 'index' => 'sku', 'width' => '200px'));

        $this->addColumn(
            'price',
            array(
                'header' => __('Price'),
                'index' => 'price',
                'type' => 'currency',
                'width' => '120px',
                'currency_code' => (string)$this->_storeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )
        );

        $this->addColumn(
            'qty',
            array('header' => __('Quantity'), 'index' => 'qty', 'type' => 'number', 'width' => '120px')
        );

        $this->addColumn(
            'total',
            array(
                'header' => __('Total'),
                'index' => 'row_total',
                'type' => 'currency',
                'width' => '120px',
                'currency_code' => (string)$this->_storeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action options for this grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('products');
        $this->getMassactionBlock()->addItem(
            'add',
            array(
                'label' => __('Add to Gift Registry'),
                'url' => $this->getUrl('adminhtml/*/add', array('id' => $this->getEntity()->getId())),
                'confirm' => __('Are you sure you want to add these products?')
            )
        );

        return $this;
    }

    /**
     * Return grid row url
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', array('id' => $row->getProductId()));
    }

    /**
     * Return gift registry entity object
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return $this->_coreRegistry->registry('current_giftregistry_entity');
    }
}

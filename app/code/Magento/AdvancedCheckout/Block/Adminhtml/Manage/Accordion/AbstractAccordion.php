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
 * Abstract class for accordion grids
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

abstract class AbstractAccordion
    extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Collection field name for using in controls
     * @var string
     */
    protected $_controlFieldName = 'entity_id';

    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'product_to_add';

    /**
     * Url to configure this grid's items
     */
    protected $_configureRoute = 'checkout/index/configureProductToAdd';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Data\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Data\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    /**
     * Initialize Grid
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setRowClickCallback('checkoutObj.productGridRowClick.bind(checkoutObj)');
        $this->setCheckboxCheckCallback('checkoutObj.productGridCheckboxCheck.bind(checkoutObj)');
        $this->setRowInitCallback('checkoutObj.productGridRowInit.bind(checkoutObj)');
    }

    /**
     * Workaround for displaying empty grid when no items found
     *
     * @return bools
     */
    public function getIsCollapsed()
    {
        return ($this->getItemsCount() == 0);
    }

    /**
     * Return items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        $collection = $this->getItemsCollection();
        if ($collection) {
            return count($collection->getItems());
        }
        return 0;
    }

    /**
     * Return items collection
     *
     * @return \Magento\Data\Collection
     */
    public function getItemsCollection()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * Prepare collection for grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->getItemsCollection());
        return parent::_prepareCollection();
    }

    /**
     * Returns special renderer for price column content
     *
     * @return null|string
     */
    protected function _getPriceRenderer()
    {
        return null;
    }

    /**
     * Prepare Grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_name', array(
            'header'    => __('Product'),
            'renderer'  => 'Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Product',
            'index'     => 'name',
            'sortable'  => false
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'renderer'  => $this->_getPriceRenderer(),
            'align'     => 'right',
            'type'      => 'currency',
            'column_css_class' => 'price',
            'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'sortable'  => false
        ));

        $this->_addControlColumns();

        return parent::_prepareColumns();
    }

    /**
     * Add columns with controls to manage added products and their quantity
     *
     * @return \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
     */
    protected function _addControlColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'field_name'=> $this->getId() ? $this->getId() : 'source_product',
            'align'     => 'center',
            'index'     => $this->_controlFieldName,
            'use_index' => true,
            'sortable'  => false
        ));

        $this->addColumn('qty', array(
            'sortable'  => false,
            'header'    => __('Quantity'),
            'renderer'  => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'right',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));

        return $this;
    }

    /**
     * Return current customer from regisrty
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return $this->_coreRegistry->registry('checkout_current_customer');
    }

    /**
     * Return current store from regisrty
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _getStore()
    {
        return $this->_coreRegistry->registry('checkout_current_store');
    }

    /**
     * Returns javascript list type of this grid
     *
     * @return string
     */
    public function getListType()
    {
        return $this->_listType;
    }

    /**
     * Returns url to configure item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        $params = array(
            'customer' => $this->_getCustomer()->getId(),
            'store' => $this->_getStore()->getId()
        );
        return $this->getUrl($this->_configureRoute, $params);
    }

    /**
     * Returns additional javascript to init this grid
     *
     * @return \Magento\Core\Model\Store
     */
    public function getAdditionalJavaScript ()
    {
        return "Event.observe(window, 'load',  function() {\n"
            . "setTimeout(function(){productConfigure.addListType('" . $this->getListType() . "', {urlFetch: '"
            . $this->getConfigureUrl() . "'})\n"
            . "});\n"
            . "checkoutObj.addSourceGrid({htmlId: '" . $this->getId() . "', listType: '" . $this->getListType()
            . "'});\n}, 10)";
    }
}

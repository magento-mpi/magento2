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
 * Accordion grid for products in compared list
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class Compared
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'compared';

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminhtmlSales;

    /**
     * @var \Magento\Catalog\Model\Product\Compare\ListCompareFactory|null
     */
    protected $_compareListFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $_catalogStockStatus;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\CatalogInventory\Model\Stock\Status $catalogStockStatus
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Sales\Helper\Admin $adminhtmlSales
     * @param \Magento\Catalog\Model\Product\Compare\ListCompareFactory $compareListFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Data\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\CatalogInventory\Model\Stock\Status $catalogStockStatus,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Sales\Helper\Admin $adminhtmlSales,
        \Magento\Catalog\Model\Product\Compare\ListCompareFactory $compareListFactory,
        array $data = array()
    ) {
        $this->_catalogStockStatus = $catalogStockStatus;
        $this->_catalogConfig = $catalogConfig;
        $this->_compareListFactory = $compareListFactory;
        $this->_adminhtmlSales = $adminhtmlSales;
        parent::__construct($context, $urlModel, $backendHelper, $collectionFactory, $coreRegistry, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_compared');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Products in the Comparison List (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Return items collection
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = $this->_catalogConfig->getProductAttributes();
            $collection = $this->_compareListFactory->create()
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId())
                ->addAttributeToSelect($attributes)
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Status::STATUS_ENABLED);
            $this->_catalogStockStatus->addIsInStockFilterToCollection($collection);
            $collection = $this->_adminhtmlSales->applySalableProductTypesFilter($collection);
            $collection->addOptionsToResult();
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('checkout/*/viewCompared', array('_current'=>true));
    }
}

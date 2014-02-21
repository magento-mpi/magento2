<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

use \Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * Accordion grid for recently viewed products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Rviewed extends AbstractAccordion
{
    /**
     * Javascript list type name for this grid
     *
     * @var string
     */
    protected $_listType = 'rviewed';

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminhtmlSales;

    /**
     * @var \Magento\Reports\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

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
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\CatalogInventory\Model\Stock\Status $catalogStockStatus
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Sales\Helper\Admin $adminhtmlSales
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Reports\Model\EventFactory $eventFactory
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Data\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\CatalogInventory\Model\Stock\Status $catalogStockStatus,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Sales\Helper\Admin $adminhtmlSales,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Reports\Model\EventFactory $eventFactory,
        array $data = array()
    ) {
        $this->_adminhtmlSales = $adminhtmlSales;
        $this->_catalogStockStatus = $catalogStockStatus;
        $this->_catalogConfig = $catalogConfig;
        $this->_productFactory = $productFactory;
        $this->_eventFactory = $eventFactory;
        parent::__construct($context, $backendHelper, $collectionFactory, $coreRegistry, $data);
    }

    /**
     * Initialize Grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_rviewed');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Recently Viewed Products (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Prepare customer wishlist product collection
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = $this->_eventFactory->create()
                ->getCollection()
                ->addStoreFilter($this->_getStore()->getWebsite()->getStoreIds())
                ->addRecentlyFiler(\Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW, $this->_getCustomer()->getId(), 0);
            $productIds = array();
            foreach ($collection as $event) {
                $productIds[] = $event->getObjectId();
            }

            $productCollection = parent::getItemsCollection();
            if ($productIds) {
                $attributes = $this->_catalogConfig->getProductAttributes();
                $productCollection = $this->_productFactory->create()->getCollection()
                    ->setStoreId($this->_getStore()->getId())
                    ->addStoreFilter($this->_getStore()->getId())
                    ->addAttributeToSelect($attributes)
                    ->addIdFilter($productIds)
                    ->addAttributeToFilter('status', ProductStatus::STATUS_ENABLED);

                $this->_catalogStockStatus->addIsInStockFilterToCollection($productCollection);
                $productCollection = $this->_adminhtmlSales
                    ->applySalableProductTypesFilter($productCollection);
                $productCollection->addOptionsToResult();
            }
            $this->setData('items_collection', $productCollection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Retrieve Grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('checkout/*/viewRecentlyViewed', array('_current'=>true));
    }

}

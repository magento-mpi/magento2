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
 * Accordion grid for Recently compared products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class Rcompared
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'rcompared';

    /**
     * @var \Magento\Adminhtml\Helper\Sales
     */
    protected $_adminhtmlSales;

    /**
     * @var \Magento\Catalog\Model\Product\Compare\ListCompareFactory
     */
    protected $_compareListFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var \Magento\Reports\Model\Resource\Event
     */
    protected $_reportsEventResource;

    /**
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Reports\Model\Resource\Event $reportsEventResource
     * @param \Magento\Adminhtml\Helper\Sales $adminhtmlSales
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Compare\ListCompareFactory $compareListFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Reports\Model\Resource\Event $reportsEventResource,
        \Magento\Adminhtml\Helper\Sales $adminhtmlSales,
        \Magento\Data\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Compare\ListCompareFactory $compareListFactory,
        array $data = array()
    ) {
        $this->_catalogConfig = $catalogConfig;
        $this->_reportsEventResource = $reportsEventResource;
        $this->_adminhtmlSales = $adminhtmlSales;
        $this->_productFactory = $productFactory;
        $this->_compareListFactory = $compareListFactory;
        parent::__construct($collectionFactory, $coreData, $context, $storeManager, $urlModel, $coreRegistry, $data);
    }

    /**
     * Initialize Grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_rcompared');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Recently Compared Products (%1)', $this->getItemsCount())
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
            $skipProducts = array();
            $collection = $this->_compareListFactory->create()
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId());
            foreach ($collection as $_item) {
                $skipProducts[] = $_item->getProductId();
            }

            // prepare products collection and apply visitors log to it
            $attributes = $this->_catalogConfig->getProductAttributes();
            if (!in_array('status', $attributes)) {
                // Status attribute is required even if it is not used in product listings
                array_push($attributes, 'status');
            }
            $productCollection = $this->_productFactory->create()->getCollection()
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->addAttributeToSelect($attributes);
            $this->_reportsEventResource->applyLogToCollection(
                $productCollection,
                \Magento\Reports\Model\Event::EVENT_PRODUCT_COMPARE,
                $this->_getCustomer()->getId(),
                0,
                $skipProducts
            );
            $productCollection = $this->_adminhtmlSales->applySalableProductTypesFilter($productCollection);
            // Remove disabled and out of stock products from the grid
            foreach ($productCollection as $product) {
                if (!$product->getStockItem()->getIsInStock() || !$product->isInStock()) {
                    $productCollection->removeItemByKey($product->getId());
                }
            }
            $productCollection->addOptionsToResult();
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
        return $this->getUrl('*/*/viewRecentlyCompared', array('_current'=>true));
    }
}

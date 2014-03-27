<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

/**
 * Catalog Product Mass Action processing model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Action extends \Magento\Model\AbstractModel
{
    /**
     * Index indexer
     *
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexIndexer;

    /**
     * Product website factory
     *
     * @var \Magento\Catalog\Model\Product\WebsiteFactory
     */
    protected $_productWebsiteFactory;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $categoryIndexer;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Catalog\Model\Product\WebsiteFactory $productWebsiteFactory
     * @param \Magento\Index\Model\Indexer $indexIndexer
     * @param \Magento\Indexer\Model\IndexerInterface $categoryIndexer
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Catalog\Model\Product\WebsiteFactory $productWebsiteFactory,
        \Magento\Index\Model\Indexer $indexIndexer,
        \Magento\Indexer\Model\IndexerInterface $categoryIndexer,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productWebsiteFactory = $productWebsiteFactory;
        $this->_indexIndexer = $indexIndexer;
        $this->categoryIndexer = $categoryIndexer;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Resource\Product\Action');
    }

    /**
     * Return product category indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getCategoryIndexer()
    {
        if (!$this->categoryIndexer->getId()) {
            $this->categoryIndexer->load(\Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID);
        }
        return $this->categoryIndexer;
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return \Magento\Catalog\Model\Resource\Product\Action
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Update attribute values for entity list per store
     *
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @return $this
     */
    public function updateAttributes($productIds, $attrData, $storeId)
    {
        $this->_eventManager->dispatch(
            'catalog_product_attribute_update_before',
            array('attributes_data' => &$attrData, 'product_ids' => &$productIds, 'store_id' => &$storeId)
        );

        $this->_getResource()->updateAttributes($productIds, $attrData, $storeId);
        $this->setData(
            array('product_ids' => array_unique($productIds), 'attributes_data' => $attrData, 'store_id' => $storeId)
        );

        // register mass action indexer event
        $this->_indexIndexer->processEntityAction(
            $this,
            \Magento\Catalog\Model\Product::ENTITY,
            \Magento\Index\Model\Event::TYPE_MASS_ACTION
        );
        if (!$this->getCategoryIndexer()->isScheduled()) {
            $this->getCategoryIndexer()->reindexList(array_unique($productIds));
        }
        return $this;
    }

    /**
     * Update websites for product action
     *
     * Allowed types:
     * - add
     * - remove
     *
     * @param array $productIds
     * @param array $websiteIds
     * @param string $type
     * @return void
     */
    public function updateWebsites($productIds, $websiteIds, $type)
    {
        if ($type == 'add') {
            $this->_productWebsiteFactory->create()->addProducts($websiteIds, $productIds);
        } else if ($type == 'remove') {
            $this->_productWebsiteFactory->create()->removeProducts($websiteIds, $productIds);
        }

        $this->setData(
            array('product_ids' => array_unique($productIds), 'website_ids' => $websiteIds, 'action_type' => $type)
        );

        // register mass action indexer event
        $this->_indexIndexer->processEntityAction(
            $this,
            \Magento\Catalog\Model\Product::ENTITY,
            \Magento\Index\Model\Event::TYPE_MASS_ACTION
        );
        if (!$this->getCategoryIndexer()->isScheduled()) {
            $this->getCategoryIndexer()->reindexList(array_unique($productIds));
        }
    }
}

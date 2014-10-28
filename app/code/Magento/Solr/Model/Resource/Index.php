<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Resource;

use Magento\CatalogSearch\Model\Resource\EngineProvider;
use Magento\Framework\StoreManagerInterface;
use Magento\Search\Model\Resource\Helper;

/**
 * Enterprise search index resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Index extends \Magento\CatalogSearch\Model\Resource\Fulltext
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $engineProvider;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param Helper $resourceHelper
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Search\Model\Resource\Helper $resourceHelper,
        StoreManagerInterface $storeManager,
        EngineProvider $engineProvider
    ) {
        $this->engineProvider = $engineProvider;
        $this->storeManager = $storeManager;
        parent::__construct($resource, $eventManager, $filter, $resourceHelper);
    }

    /**
     * Return array of price data per customer and website by products
     *
     * @param null|array $productIds
     * @return array
     */
    protected function _getCatalogProductPriceData($productIds = null)
    {
        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select()->from(
            $this->getTable('catalog_product_index_price'),
            array('entity_id', 'customer_group_id', 'website_id', 'min_price')
        );

        if ($productIds) {
            $select->where('entity_id IN (?)', $productIds);
        }

        $result = array();
        foreach ($adapter->fetchAll($select) as $row) {
            $result[$row['website_id']][$row['entity_id']][$row['customer_group_id']] = round($row['min_price'], 2);
        }

        return $result;
    }

    /**
     * Retrieve price data for product
     *
     * @param null|array $productIds
     * @param int $storeId
     * @return array
     */
    public function getPriceIndexData($productIds, $storeId)
    {
        $priceProductsIndexData = $this->_getCatalogProductPriceData($productIds);

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        if (!isset($priceProductsIndexData[$websiteId])) {
            return array();
        }

        return $priceProductsIndexData[$websiteId];
    }

    /**
     * Prepare system index data for products.
     *
     * @param int $storeId
     * @param null|array $productIds
     * @return array
     */
    public function getCategoryProductIndexData($storeId = null, $productIds = null)
    {
        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select()->from(
            array($this->getTable('catalog_category_product_index')),
            array('category_id', 'product_id', 'position', 'store_id')
        )->where(
            'store_id = ?',
            $storeId
        );

        if ($productIds) {
            $select->where('product_id IN (?)', $productIds);
        }

        $result = array();
        foreach ($adapter->fetchAll($select) as $row) {
            $result[$row['product_id']][$row['category_id']] = $row['position'];
        }

        return $result;
    }

    /**
     * Retrieve moved categories product ids
     *
     * @param int $categoryId
     * @return array
     */
    public function getMovedCategoryProductIds($categoryId)
    {
        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select()->distinct()->from(
            array('c_p' => $this->getTable('catalog_category_product')),
            array('product_id')
        )->join(
            array('c_e' => $this->getTable('catalog_category_entity')),
            'c_p.category_id = c_e.entity_id',
            array()
        )->where(
            $adapter->quoteInto('c_e.path LIKE ?', '%/' . $categoryId . '/%')
        )->orWhere(
            'c_p.category_id = ?',
            $categoryId
        );

        return $adapter->fetchCol($select);
    }
}

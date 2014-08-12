<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Store view service
 */
class StoreViewService
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        StoreManagerInterface $storeManager
    ) {
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        $this->storeManager = $storeManager;
    }

    /**
     * Check is root category for store view
     *
     * @param int $categoryId
     * @param int $storeId
     * @return bool
     */
    public function isRootCategoryForStore($categoryId, $storeId)
    {
        return $categoryId == $this->storeManager->getStore($storeId)->getRootCategoryId();
    }

    /**
     * Check that product has overridden url key for specific store
     *
     * @param int $storeId
     * @param int $productId
     * @return bool
     */
    public function doesProductHaveOverriddenUrlKeyForStore($storeId, $productId)
    {
        return $this->doesEntityHaveOverriddenUrlKeyForStore($storeId, $productId, Product::ENTITY);
    }

    /**
     * Check that category has overridden url key for specific store
     *
     * @param int $storeId
     * @param int $categoryId
     * @return bool
     */
    public function doesCategoryHaveOverriddenUrlKeyForStore($storeId, $categoryId)
    {
        return $this->doesEntityHaveOverriddenUrlKeyForStore($storeId, $categoryId, Category::ENTITY);
    }

    /**
     * Check that entity has overridden url key for specific store
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    protected function doesEntityHaveOverriddenUrlKeyForStore($storeId, $entityId, $entityType)
    {
        $attribute = $this->eavConfig->getAttribute($entityType, 'url_key');
        $select = $this->connection->select()
            ->from($attribute->getBackendTable(), 'store_id')
            ->where('attribute_id = ?', $attribute->getId())
            ->where('entity_id = ?', $entityId);

        return in_array($storeId, $this->connection->fetchCol($select));
    }
}

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
use Magento\Store\Model\Store;
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
     * Check is global scope
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isGlobalScope($storeId)
    {
        return null === $storeId || $storeId == Store::DEFAULT_STORE_ID;
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
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'url_key');
        $select = $this->connection->select()
            ->from($attribute->getBackendTable(), 'store_id')
            ->where('attribute_id = ?', $attribute->getId())
            ->where('entity_id = ?', $productId);

        return in_array($storeId, $this->connection->fetchCol($select));
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
        $attribute = $this->eavConfig->getAttribute(Category::ENTITY, 'url_key');
        $select = $this->connection->select()
            ->from($attribute->getBackendTable(), 'store_id')
            ->where('attribute_id = ?', $attribute->getId())
            ->where('entity_id = ?', $categoryId);

        return in_array($storeId, $this->connection->fetchCol($select));
    }
}

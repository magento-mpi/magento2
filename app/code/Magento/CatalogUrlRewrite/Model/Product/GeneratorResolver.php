<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\CatalogUrlRewrite\Model\Product\GeneratorFactory;
use Magento\Framework\App\Resource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;

/**
 * Product generator resolver
 */
class GeneratorResolver
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\Product\Generator
     */
    protected $generator;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var array
     */
    protected $fallbackStoreData = [];

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\CatalogUrlRewrite\Model\Product\GeneratorFactory $generatorFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(
        Product $product,
        GeneratorFactory $generatorFactory,
        StoreManagerInterface $storeManager,
        Config $eavConfig,
        Resource $resource
    )
    {
        $this->storeManager = $storeManager;
        $this->eavConfig = $eavConfig;
        $this->product = $product;
        $this->connection = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        $this->generator = $generatorFactory->create(['product' => $product]);
    }

    /**
     * Generate list of urls
     *
     * @return \Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product[]
     */
    public function generate()
    {
        $storeId = $this->product->getStoreId();
        if ($this->isDefaultStore($storeId)) {
            $urls = [];
            foreach ($this->storeManager->getStores() as $store) {
                if ($this->isNeedToProcessForStore($store->getStoreId())) {
                    $urls = array_merge($urls, $this->generator->generatePerStore($store->getStoreId()));
                }
            }
        } else {
            $urls = $this->generator->generatePerStore($storeId);
        }
        return $urls;
    }

    /**
     * Whether the store is default
     *
     * @param int|null $storeId
     * @return bool
     */
    protected function isDefaultStore($storeId)
    {
        return is_null($storeId) || $storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

    /**
     * If product saved on default store view, then need to check specific url_key for other stores
     *
     * @param int $storeId
     * @return bool
     */
    protected function isNeedToProcessForStore($storeId)
    {
        if (!$this->fallbackStoreData) {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'url_key');
            $select = $this->connection->select();
            $select->from($attribute->getBackendTable(), 'store_id')
                ->where('attribute_id = ?', $attribute->getId())
                ->where('entity_id = ?', $this->product->getId());
            $this->fallbackStoreData = $this->connection->fetchCol($select);
        }
        return !in_array($storeId, $this->fallbackStoreData);
    }
}

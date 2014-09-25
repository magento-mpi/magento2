<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\StoreManagerInterface;
use Magento\Store\Model\Store;

class DataProvider implements DataProviderInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $eavConfig
     * @param Resource $resource
     */
    public function __construct(Config $eavConfig, Resource $resource, StoreManagerInterface $storeManager)
    {
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSet(BucketInterface $bucket, RequestInterface $request)
    {
        $currentStore = $request->getScopeDimension()->getValue();
        $currentStoreId = $this->storeManager->getStore($currentStore)->getId();
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $table = $attribute->getBackendTable();

        $ifNullCondition = $this->getConnection()->getIfNullSql('current_store.value', 'main_table.value');

        $select = $this->getSelect();
        $select->from(['main_table' => $table], null)
            ->joinLeft(
                ['current_store' => $table],
                'current_store.attribute_id = main_table.attribute_id AND current_store.store_id = ' . $currentStoreId,
                null
            )
            ->columns([BucketInterface::FIELD_VALUE => $ifNullCondition])
            ->where('main_table.attribute_id = ?', $attribute->getAttributeId())
            ->where('main_table.store_id = ?', Store::DEFAULT_STORE_ID);

        return $select;
    }

    /**
     * @return Select
     */
    private function getSelect()
    {
        return $this->getConnection()->select();
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }
}

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

    public function __construct(Config $eavConfig, Resource $resource)
    {
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getTermDataSet(BucketInterface $bucket, RequestInterface $request)
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $table = $attribute->getBackendTable();

        $select = $this->getSelect();
        $select->from(['main_table' => $table], null)
            ->joinLeft(
                ['current_store' => $table],
                'current_store.attribute_id = main_table.attribute_id AND current_store.store_id = 1',
                null
            )
            ->columns(
                ['value' => $this->getConnection()->getIfNullSql('current_store.value', 'main_table.value')]
            )
            ->where('main_table.attribute_id = ?', $attribute->getAttributeId())
            ->where('main_table.store_id = ?', 0);

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

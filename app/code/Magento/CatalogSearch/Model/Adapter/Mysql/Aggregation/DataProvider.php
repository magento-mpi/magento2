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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Catalog\Model\Layer\Filter\Price\Range\Proxy as RangeProxy;
use Magento\Framework\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class DataProvider implements DataProviderInterface
{
    const XML_PATH_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';
    const XML_PATH_RANGE_STEP = 'catalog/layered_navigation/price_range_step';
    const XML_PATH_RANGE_MAX_INTERVALS = 'catalog/layered_navigation/price_range_max_intervals';

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
     * @var RangeProxy
     */
    private $range;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Config $eavConfig
     * @param Resource|Resource $resource
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param RangeProxy $range
     * @internal param Range $range
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        RangeProxy $range
    ) {
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->range = $range;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSet(BucketInterface $bucket, array $dimensions)
    {
        $currentStore = $dimensions['scope']->getValue();
        $currentStoreId = $this->storeManager->getStore($currentStore)
            ->getId();
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $table = $attribute->getBackendTable();

        $ifNullCondition = $this->getConnection()
            ->getIfNullSql('current_store.value', 'main_table.value');

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
     * {@inheritdoc}
     */
    public function execute(Select $select)
    {
        return $this->getConnection()
            ->fetchAssoc($select);
    }

    /**
     * @param int[] $entityIds
     * @return array
     */
    public function getAggregations(array $entityIds)
    {
        $select = $this->getSelect();

        $tableName = $this->getConnection()
            ->getTableName('catalog_product_index_price');
        $select->from($tableName, [])
            ->where('entity_id IN (?)', $entityIds)
            ->columns(
                [
                    'count' => 'count(DISTINCT entity_id)',
                    'max' => 'MAX(min_price)',
                    'min' => 'MIN(min_price)',
                    'std' => 'STDDEV_SAMP(min_price)'
                ]
            );
        $result = $this->getConnection()
            ->fetchRow($select);

        return $result;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'interval_division_limit' => (int)$this->scopeConfig->getValue(
                self::XML_PATH_INTERVAL_DIVISION_LIMIT,
                ScopeInterface::SCOPE_STORE
            ),
            'range_step' => (double)$this->scopeConfig->getValue(
                self::XML_PATH_RANGE_STEP,
                ScopeInterface::SCOPE_STORE
            ),
            'min_range_power' => 10,
            'max_intervals_number' => (int)$this->scopeConfig->getValue(
                self::XML_PATH_RANGE_MAX_INTERVALS,
                ScopeInterface::SCOPE_STORE
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregation($range, array $entityIds, $aggregationType)
    {
        $select = $this->getSelect();

        $rangeExpr = new \Zend_Db_Expr("FLOOR(min_price / {$range}) + 1");
        $tableName = $this->getConnection()
            ->getTableName('catalog_product_index_price');
        $select->from($tableName, [])
            ->where('entity_id IN (?)', $entityIds)
            ->columns(['range' => $rangeExpr, 'count' => 'COUNT(*)'])
            ->group($rangeExpr)
            ->order("{$rangeExpr} ASC");

        return $this->getConnection()
            ->fetchPairs($select);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($range, array $dbRanges)
    {
        $data = [];
        if (!empty($dbRanges)) {
            $lastIndex = array_keys($dbRanges);
            $lastIndex = $lastIndex[count($lastIndex) - 1];

            foreach ($dbRanges as $index => $count) {
                $fromPrice = $index == 1 ? '' : ($index - 1) * $range;
                $toPrice = $index == $lastIndex ? '' : $index * $range;

                $data[] = [
                    'from' => $fromPrice,
                    'to' => $toPrice,
                    'count' => $count
                ];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getRange()
    {
        return $this->range->getPriceRange();
    }

    /**
     * @return Select
     */
    private function getSelect()
    {
        return $this->getConnection()
            ->select();
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }
}

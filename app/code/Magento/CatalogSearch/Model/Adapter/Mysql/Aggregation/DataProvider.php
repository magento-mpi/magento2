<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\ScopeResolverInterface;

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
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var Range
     */
    private $range;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @param Config $eavConfig
     * @param Resource $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param Range $range
     * @param Session $customerSession
     * @internal param Range $range
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        ScopeResolverInterface $scopeResolver,
        ScopeConfigInterface $scopeConfig,
        Range $range,
        Session $customerSession
    ) {
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->range = $range;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSet(BucketInterface $bucket, array $dimensions)
    {
        $currentScope = $dimensions['scope']->getValue();

        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());

        if ($attribute->getAttributeCode() == 'price') {
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->scopeResolver->getScope($currentScope);
            if (!$store instanceof \Magento\Store\Model\Store) {
                throw new \RuntimeException('Illegal scope resolved');
            }
            $table = $this->resource->getTableName('catalog_product_index_price');
            $select = $this->getSelect();
            $select->from(['main_table' => $table], null)
                ->columns([BucketInterface::FIELD_VALUE => 'main_table.min_price'])
                ->where('main_table.customer_group_id = ?', $this->customerSession->getCustomerGroupId())
                ->where('main_table.website_id = ?', $store->getWebsiteId());

        } else {
            $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();
            $table = $attribute->getBackendTable();
            $ifNullCondition = $this->getConnection()
                ->getIfNullSql('current_store.value', 'main_table.value');

            $select = $this->getSelect();
            $select->from(['main_table' => $table], null)
                ->joinLeft(
                    ['current_store' => $table],
                    'current_store.attribute_id = main_table.attribute_id AND current_store.store_id = ' . $currentScopeId,
                    null
                )
                ->columns([BucketInterface::FIELD_VALUE => $ifNullCondition])
                ->where('main_table.attribute_id = ?', $attribute->getAttributeId())
                ->where('main_table.store_id = ?', Store::DEFAULT_STORE_ID);
        }

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
        $aggregation = [
            'count' => 'count(DISTINCT entity_id)',
            'max' => 'MAX(min_price)',
            'min' => 'MIN(min_price)',
            'std' => 'STDDEV_SAMP(min_price)'
        ];


        $select = $this->getSelect();

        $tableName = $this->resource->getTableName('catalog_product_index_price');
        $select->from($tableName, [])
            ->where('entity_id IN (?)', $entityIds)
            ->columns($aggregation);

        $select = $this->setCustomerGroupId($select);

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
    public function getAggregation(BucketInterface $bucket, array $dimensions, $range, array $entityIds)
    {
        $select = $this->getDataSet($bucket, $dimensions);
        $column = $select->getPart(Select::COLUMNS)[0];
        $select->reset(Select::COLUMNS);
        $rangeExpr = new \Zend_Db_Expr(
            $this->getConnection()->quoteInto('(FLOOR(' . $column[1] . ' / ? ) + 1)', $range)
        );

        $select
            ->columns(['range' => $rangeExpr])
            ->columns(['metrix' => 'COUNT(*)'])
            ->where('main_table.entity_id in (?)', $entityIds)
            ->group('range')
            ->order('range');
        $s = $select . '';
        $result = $this->getConnection()->fetchPairs($select);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($range, array $dbRanges)
    {
        $data = [];
        if (!empty($dbRanges)) {

            foreach ($dbRanges as $index => $count) {
                $fromPrice = ($index - 1) * $range;
                $toPrice = $index * $range;

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

    /**
     * @param Select $select
     * @return Select
     */
    private function setCustomerGroupId($select)
    {
        return $select->where('customer_group_id = ?', $this->customerSession->getCustomerGroupId());
    }
}

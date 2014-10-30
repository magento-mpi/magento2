<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Algorithm;

use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Resource;
use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Dynamic\Algorithm\DataProviderInterface;
use Magento\Store\Model\ScopeInterface;

class DataProvider implements DataProviderInterface
{
    const XML_PATH_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';
    const XML_PATH_RANGE_STEP = 'catalog/layered_navigation/price_range_step';
    const XML_PATH_RANGE_MAX_INTERVALS = 'catalog/layered_navigation/price_range_max_intervals';

    /**
     * @var Resource|Resource
     */
    private $resource;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Range
     */
    private $range;

    /**
     * @param Resource $resource
     * @param ScopeConfigInterface $scopeConfig
     * @param Range $range
     */
    public function __construct(Resource $resource, ScopeConfigInterface $scopeConfig, Range $range)
    {
        $this->resource = $resource;
        $this->scopeConfig = $scopeConfig;
        $this->range = $range;
    }

    /**
     * @param int[] $entityIds
     * @return array
     */
    public function getAggregations(array $entityIds)
    {
        $select = $this->getConnection()
            ->select();

        $select->from('catalog_product_index_price', [])
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
    public function getCount($range, array $entityIds)
    {
        $select = $this->getConnection()
            ->select();

        $rangeExpr = new \Zend_Db_Expr("FLOOR(min_price / {$range}) + 1");
        $select->from('catalog_product_index_price', [])
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
     * @return false|AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Config::DEFAULT_SETUP_CONNECTION);
    }
}
 
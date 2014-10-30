<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Algorithm;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Resource;
use Magento\Framework\App\Resource\Config;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Dynamic\Algorithm\DataProviderInterface;
use Magento\Store\Model\ScopeInterface;

class DataProvider implements DataProviderInterface
{
    const XML_PATH_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';

    /**
     * @var Resource|Resource
     */
    private $resource;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Resource|Resource $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Resource $resource, ScopeConfigInterface $scopeConfig)
    {
        $this->resource = $resource;
        $this->scopeConfig = $scopeConfig;
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
     * {@inheritdoc}
     */
    public function getIntervalDivisionLimit()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_INTERVAL_DIVISION_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return false|AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Config::DEFAULT_SETUP_CONNECTION);
    }
}
 
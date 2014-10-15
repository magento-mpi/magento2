<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Filter;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Adapter\Mysql\Filter\PreprocessorInterface;
use Magento\Framework\Search\Request\FilterInterface;

class Preprocessor implements PreprocessorInterface
{
    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @param ConditionManager $conditionManager
     * @param ScopeResolverInterface $scopeResolver
     * @param Config $config
     * @param Resource $resource
     */
    public function __construct(
        ConditionManager $conditionManager,
        ScopeResolverInterface $scopeResolver,
        Config $config,
        Resource $resource
    ) {
        $this->conditionManager = $conditionManager;
        $this->scopeResolver = $scopeResolver;
        $this->config = $config;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FilterInterface $filter, $isNegation, $query)
    {
        if (method_exists($filter, 'getField')) {
            $resultQuery =  $this->processQueryWithField($filter, $isNegation, $query);
        } else {
            $resultQuery = $this->conditionManager->wrapBrackets($query);
        }
        return $resultQuery;
    }

    /**
     * @param FilterInterface $filter
     * @param bool $isNegation
     * @param string $query
     * @return string
     */
    private function processQueryWithField(FilterInterface $filter, $isNegation, $query)
    {
        $currentStoreId = $this->scopeResolver->getScope()->getId();

        $attribute = $this->config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $filter->getField());
        $select = $this->getSelect();
        $table = $attribute->getBackendTable();
        if ($filter->getField() == 'price') {
            $query = str_replace('price', 'min_price', $query);
            $select->from(['main_table' => $this->resource->getTableName('catalog_product_index_price')], 'entity_id')
                ->where($query);
        } else {
            if ($attribute->isStatic()) {
                $select->from(['main_table' => $table], 'entity_id')
                    ->where($query);
            } else {

                $ifNullCondition = $this->getConnection()->getIfNullSql('current_store.value', 'main_table.value');

                $select->from(['main_table' => $table], 'entity_id')
                    ->joinLeft(
                        ['current_store' => $table],
                        'current_store.attribute_id = main_table.attribute_id AND current_store.store_id = '
                        . $currentStoreId,
                        null
                    )
                    ->columns([$filter->getField() => $ifNullCondition])
                    ->where(
                        'main_table.attribute_id = ?',
                        $attribute->getAttributeId()
                    )
                    ->where('main_table.store_id = ?', 0)
                    ->having($query);
            }
        }

        return 'product_id ' . ($isNegation ? 'NOT' : '') . ' IN (
                select entity_id from  ' . $this->conditionManager->wrapBrackets($select) . '
             as filter)';
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    private function getSelect()
    {
        return $this->getConnection()->select();
    }
}

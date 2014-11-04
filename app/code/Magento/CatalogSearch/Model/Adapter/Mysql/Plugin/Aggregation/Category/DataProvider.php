<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Plugin\Aggregation\Category;

use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Catalog\Model\Layer\Category;

class DataProvider
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * Category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Resource $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param Category $layer
     */
    public function __construct(
        Resource $resource,
        ScopeResolverInterface $scopeResolver,
        Category $layer
    ) {
        $this->resource = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->layer = $layer;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject
     * @param callable $proceed
     * @param BucketInterface $bucket
     * @param Dimension[] $dimensions
     *
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetDataSet(
        \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions
    ) {
        if ($bucket->getField() == 'category_ids') {
            $currentScope = $dimensions['scope']->getValue();
            $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();
            $currenCategory = $this->layer->getCurrentCategory();

            $derivedTable = $this->getSelect();
            $derivedTable->from(
                ['main_table' => $this->resource->getTableName('catalog_category_product_index')],
                [
                    'entity_id' => 'product_id',
                    'value' => 'category_id'
                ]
            )->where('main_table.store_id = ?', $currentScopeId);

            if (!empty($currenCategory)) {
                $derivedTable->join(
                    array('category' => $this->resource->getTableName('catalog_category_entity')),
                    'main_table.category_id = category.entity_id',
                    array()
                )->where('`category`.`path` LIKE ?', $currenCategory->getPath() . '%')
                ->where('`category`.`level` > ?', $currenCategory->getLevel());
            }
            $select = $this->getSelect();
            $select->from(['main_table' => $derivedTable]);
            return $select;
        }
        return $proceed($bucket, $dimensions);
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

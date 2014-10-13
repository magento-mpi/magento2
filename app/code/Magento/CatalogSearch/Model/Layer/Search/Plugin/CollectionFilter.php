<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Search\Plugin;

use Magento\Catalog\Model\Category;
use Magento\CatalogSearch\Model\Resource\Fulltext\Collection as FulltextCollection;
use Magento\Search\Model\QueryFactory;

class CollectionFilter
{

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;

    /**
     * @param QueryFactory $queryFactory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param $collection
     * @param Category $category
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundFilter(
        \Magento\Catalog\Model\Layer\Search\CollectionFilter $subject,
        \Closure $proceed,
        $collection,
        Category $category
    ) {
        $proceed($collection, $category);
        if ($collection instanceof FulltextCollection) {
            $collection->addSearchFilter($this->queryFactory->get()->getQueryText());
        }
    }
}

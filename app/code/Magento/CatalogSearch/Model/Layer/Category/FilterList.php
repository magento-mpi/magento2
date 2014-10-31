<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Layer\Category;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @var \Magento\Framework\Search\Request\Builder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    private $searchEngine;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        array $filters = array()
    ) {
        parent::__construct($objectManager, $filterableAttributes, $filters);
        $this->requestBuilder = $requestBuilder;
        $this->requestBuilder->bindDimension('scope', $scopeResolver->getScope()->getId());
        $this->requestBuilder->setRequestName('quick_search_container');
        $this->searchEngine = $searchEngine;
    }

    /**
     * Create filter
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\Layer $layer
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected function createAttributeFilter(
        \Magento\Catalog\Model\Resource\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $curCategoryId = $layer->getCurrentCategory()->getId();
        $this->requestBuilder->bind('category_ids', $curCategoryId);

        $filterClassName = $this->getAttributeFilterClass($attribute);
        $filter = $this->objectManager->create(
            $filterClassName,
            array(
                'layer' => $layer,
                'requestBuilder' => $this->requestBuilder,
                'data' => array('attribute_model' => $attribute),
            )
        );
        return $filter;
    }

    /**
     * Prepare Filters
     *
     * @return void
     */
    public function prepareFilters()
    {
        //$queryRequest = $this->requestBuilder->create();
        /** @var \Magento\Framework\Search\Response\QueryResponse $queryResponse */
        /*
        $queryResponse = $this->searchEngine->search($queryRequest);
        $aggregations = $queryResponse->getAggregations();
        foreach ($this->filters as $filter) {
            try {
                $attribute = $filter->getAttributeModel();
            } catch (\Exception $e) {
                continue;
            }
            $filterAttributeCode = $attribute->getAttributeCode();
            if ($filterAttributeCode == 'price') {
                continue;
            }
            $values = array();
            foreach($aggregations->getBucket($filterAttributeCode . '_bucket')->getValues() as $value) {
                $values[] = $value->getMetrics();
            }
            $filter->setItems($values);
        }*/
    }
}

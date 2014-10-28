<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Search;

use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory;

class RequestGenerator
{
    /**
     * @var CollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @param CollectionFactory $productAttributeCollectionFactory
     */
    public function __construct(CollectionFactory $productAttributeCollectionFactory)
    {
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }

    /**
     * Generate dynamic fields requests
     *
     * @return array
     */
    public function generate()
    {
        $requests = [];
        $requests['quick_search_container'] = $this->generateQuickSearchRequest();
        $requests['advanced_search_container'] = $this->generateAdvancedSearchRequest();
        return $requests;
    }

    /**
     * Generate quick search request
     *
     * @return array
     */
    private function generateQuickSearchRequest()
    {
        $request = [];
        foreach ($this->getSearchableAttributes() as $attribute) {
            if ($attribute->getIsFilterable()) {
                $queryName = $attribute->getAttributeCode() . '_query';
                $request['queries']['quick_search_container']['queryReference'][] = [
                    'clause' => 'should',
                    'ref' => $queryName,
                ];
                $filterName = $attribute->getAttributeCode() . '_filter';
                $request['queries'][$queryName] = [
                    'name' => $queryName,
                    'type' => 'filteredQuery',
                    'filterReference' => [['ref' => $filterName]]
                ];
                $request['filters'][$filterName] = [
                    'type' => 'termFilter',
                    'name' => $filterName,
                    'field' => $attribute->getAttributeCode(),
                    'value' => '$' . $attribute->getAttributeCode() . '$',
                ];

                if (!in_array($attribute->getAttributeCode(), ['price', 'catagory_ids'])) {
                    $bucketName = $attribute->getAttributeCode() . '_bucket';
                    $request['aggregations'][$bucketName] = [
                        'type' => 'termBucket',
                        'name' => $bucketName,
                        'field' => $attribute->getAttributeCode(),
                        [["type" => "count"]],
                    ];
                }
            }
            /** @var $attribute Attribute */
            if (in_array($attribute->getAttributeCode(), ['price', 'sku'])
                || !$attribute->getIsSearchable()
            ) {
                //same fields have special semantics
                continue;
            }
            $request['queries']['search']['match'][] = [
                'field' => $attribute->getAttributeCode(),
                'boost' => $attribute->getSearchWeight() ?: 1,
            ];
        }
        return $request;
    }

    /**
     * Retrieve searchable attributes
     *
     * @return \Traversable
     */
    protected function getSearchableAttributes()
    {
        /** @var \Magento\Catalog\Model\Resource\Product\Attribute\Collection $productAttributes */
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $productAttributes->addFieldToFilter(
            ['is_searchable', 'is_visible_in_advanced_search', 'is_filterable'],
            [1, 1, 1]
        );

        return $productAttributes;
    }

    /**
     * Generate advanced search request
     *
     * @return array
     */
    private function generateAdvancedSearchRequest()
    {
        $request = [];
        foreach ($this->getSearchableAttributes() as $attribute) {
            /** @var $attribute Attribute */
            if (!$attribute->getIsVisibleInAdvancedSearch()) {
                continue;
            }
            if (in_array($attribute->getAttributeCode(), ['price', 'sku'])) {
                //same fields have special semantics
                continue;
            }

            $queryName = $attribute->getAttributeCode() . '_query';
            $request['queries']['advanced_search_container']['queryReference'][] = [
                'clause' => 'should',
                'ref' => $queryName,
            ];
            switch ($attribute->getBackendType()) {
                case 'static':
                    break;
                case 'text':
                case 'varchar':
                    if ($attribute->getFrontendInput() === 'multiselect') {
                        $filterName = $attribute->getAttributeCode() . '_filter';
                        $request['queries'][$queryName] = [
                            'name' => $queryName,
                            'type' => 'filteredQuery',
                            'filterReference' => [['ref' => $filterName]]
                        ];

                        $request['filters'][$filterName] = [
                            'type' => 'wildcardFilter',
                            'name' => $filterName,
                            'field' => $attribute->getAttributeCode(),
                            'value' => '$' . $attribute->getAttributeCode() . '$',
                        ];
                    } else {
                        $request['queries'][$queryName] = [
                            'name' => $queryName,
                            'type' => 'matchQuery',
                            'value' => '$' . $attribute->getAttributeCode() . '$',
                            'match' => [
                                [
                                    'field' => $attribute->getAttributeCode(),
                                    'boost' => $attribute->getSearchWeight() ?: 1,
                                ]
                            ]
                        ];
                    }
                    break;
                case 'decimal':
                case 'datetime':
                case 'date':
                    $filterName = $attribute->getAttributeCode() . '_filter';
                    $request['queries'][$queryName] = [
                        'name' => $queryName,
                        'type' => 'filteredQuery',
                        'filterReference' => [['ref' => $filterName]]
                    ];
                    $request['filters'][$filterName] = [
                        'field' => $attribute->getAttributeCode(),
                        'name' => $filterName,
                        'type' => 'rangeFilter',
                        'from' => '$' . $attribute->getAttributeCode() . '.from$',
                        'to' => '$' . $attribute->getAttributeCode() . '.to$',
                    ];
                    break;
                default:
                    $filterName = $attribute->getAttributeCode() . '_filter';
                    $request['queries'][$queryName] = [
                        'name' => $queryName,
                        'type' => 'filteredQuery',
                        'filterReference' => [['ref' => $filterName]]
                    ];

                    $request['filters'][$filterName] = [
                        'type' => 'termFilter',
                        'name' => $filterName,
                        'field' => $attribute->getAttributeCode(),
                        'value' => '$' . $attribute->getAttributeCode() . '$',
                    ];
            }
        }
        return $request;
    }
}

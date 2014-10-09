<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Search;

class RequestGenerator
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
    ) {
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
            /** @var $attribute \Magento\Catalog\Model\Product\Attribute */
            if (in_array($attribute->getAttributeCode(), ['price', 'sku'])) {
                //same fields have special semantics
                continue;
            }
            $request['queries']['quick_search_container']['match'][] = [
                'field' => $attribute->getAttributeCode(),
                'boost' => $attribute->getSearchWeight() ?: 1,
            ];
        }
        return $request;
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
            /** @var $attribute \Magento\Catalog\Model\Product\Attribute */
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
                    break;
                case 'decimal':
                case 'date':
                    $filterName = $attribute->getAttributeCode() . '_filter';
                    $request['queries'][$queryName] = [
                        'name' => $queryName,
                        'type' => 'filteredQuery',
                        'filterReference' => [['ref' => $filterName]]
                    ];
                    $request['filters'][$filterName] = [
                        'field' => $attribute->getAttributeCode(),
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
                        'field' => $attribute->getAttributeCode(),
                        'value' => '$' . $attribute->getAttributeCode() . '$',
                    ];
            }
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
        $productAttributes->addFieldToFilter('is_searchable', 1);

        return $productAttributes;
    }
}

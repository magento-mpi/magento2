<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer;

use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Catalog\Model\Layer\Filter;

class FilterList
{
    const CATEGORY_FILTER   = 'category';
    const ATTRIBUTE_FILTER  = 'attribute';
    const PRICE_FILTER      = 'price';
    const DECIMAL_FILTER    = 'decimal';

    /**
     * Filter factory
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var FilterableAttributeList
     */
    protected $filterableAttributes;

    /**
     * @var string[]
     */
    protected $filters = array(
        self::CATEGORY_FILTER  => 'Magento\Catalog\Model\Layer\Filter\Category',
        self::ATTRIBUTE_FILTER => 'Magento\Catalog\Model\Layer\Filter\Attribute',
        self::PRICE_FILTER     => 'Magento\Catalog\Model\Layer\Filter\Price',
        self::DECIMAL_FILTER   => 'Magento\Catalog\Model\Layer\Filter\Decimal',
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param FilterableAttributeList $filterableAttributes
     * @param array $filters
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        FilterableAttributeList $filterableAttributes,
        array $filters = array()
    ) {
        $this->objectManager = $objectManager;
        $this->filterableAttributes = $filterableAttributes;

        /** Override default filter type models */
        $this->filters = array_merge($this->filters, $filters);
    }

    /**
     * Get filters list
     *
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters()
    {
        $filters = array(
            $this->objectManager->create($this->filters[self::CATEGORY_FILTER]),
        );
        foreach ($this->filterableAttributes->getList() as $attibute) {
            $filters[] = $this->createAttributeFilter($attibute);
        }
        return $filters;
    }

    /**
     * Create filter model
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @return \Magento\Catalog\Model\Layer\Filter\FilterInterface
     */
    protected function createAttributeFilter(\Magento\Catalog\Model\Resource\Eav\Attribute $attribute)
    {
        $filterClassName = $this->filters[self::ATTRIBUTE_FILTER];

        if ($attribute->getAttributeCode() == 'price') {
            $filterClassName = $this->filters[self::PRICE_FILTER];
        } elseif ($attribute->getBackendType() == 'decimal') {
            $filterClassName = $this->filters[self::DECIMAL_FILTER];
        }

        $filter = $this->objectManager->create(
            $filterClassName,
            array('data' => array('attribute_model' => $attribute))
        );
        return $filter;
    }
}

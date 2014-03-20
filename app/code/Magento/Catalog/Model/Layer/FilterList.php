<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer;

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
     * @var FilterableAttributeListInterface
     */
    protected $filterableAttributes;

    /**
     * @var string[]
     */
    protected $filterTypes = array(
        self::CATEGORY_FILTER  => 'Magento\Catalog\Model\Layer\Filter\Category',
        self::ATTRIBUTE_FILTER => 'Magento\Catalog\Model\Layer\Filter\Attribute',
        self::PRICE_FILTER     => 'Magento\Catalog\Model\Layer\Filter\Price',
        self::DECIMAL_FILTER   => 'Magento\Catalog\Model\Layer\Filter\Decimal',
    );

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    protected $filters = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param array $filters
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        array $filters = array()
    ) {
        $this->objectManager = $objectManager;
        $this->filterableAttributes = $filterableAttributes;

        /** Override default filter type models */
        $this->filterTypes = array_merge($this->filterTypes, $filters);
    }

    /**
     * Retrieve list of filters
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!count($this->filters)) {
            $this->filters = array(
                $this->objectManager->create($this->filterTypes[self::CATEGORY_FILTER], array('layer' => $layer)),
            );
            foreach ($this->filterableAttributes->getList() as $attibute) {
                $this->filters[] = $this->createAttributeFilter($attibute, $layer);
            }
        }
        return $this->filters;
    }

    /**
     * Create filter
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\Layer $layer
     * @return mixed
     */
    protected function createAttributeFilter(
        \Magento\Catalog\Model\Resource\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $filterClassName = $this->filterTypes[self::ATTRIBUTE_FILTER];

        if ($attribute->getAttributeCode() == 'price') {
            $filterClassName = $this->filterTypes[self::PRICE_FILTER];
        } elseif ($attribute->getBackendType() == 'decimal') {
            $filterClassName = $this->filterTypes[self::DECIMAL_FILTER];
        }

        $filter = $this->objectManager->create(
            $filterClassName,
            array('data' => array('attribute_model' => $attribute), 'layer' => $layer)
        );
        return $filter;
    }
}

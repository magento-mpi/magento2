<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Category;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributes
     * @param \Magento\Search\Helper\Data $helper
     * @param array $filters
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributes,
        \Magento\Search\Helper\Data $helper,
        array $filters = array()
    ) {
        $this->helper = $helper;
        if ($helper->getIsEngineAvailableForNavigation()) {
            $this->filterTypes[self::CATEGORY_FILTER]  = 'Magento\Search\Model\Catalog\Layer\Filter\Category';
            $this->filterTypes[self::ATTRIBUTE_FILTER] = 'Magento\Search\Model\Catalog\Layer\Filter\Attribute';
            $this->filterTypes[self::PRICE_FILTER]     = 'Magento\Search\Model\Catalog\Layer\Filter\Price';
            $this->filterTypes[self::DECIMAL_FILTER]   = 'Magento\Search\Model\Catalog\Layer\Filter\Decimal';
        }
        parent::__construct($objectManager, $filterableAttributes, $filters);
    }

    /**
     * Retrieve list of filters
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|\Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!count($this->filters)) {
            $filters = parent::getFilters($layer);
            if ($this->helper->isThirdPartSearchEngine() && $this->helper->getIsEngineAvailableForNavigation()) {
                foreach ($filters as $filter) {
                    $filter->addFacetCondition();
                }
            }
        }
        return $this->filters;
    }
}

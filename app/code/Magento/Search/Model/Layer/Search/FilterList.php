<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param FilterableAttributeList $filterableAttributes
     * @param \Magento\Search\Helper\Data $helper
     * @param array $filters
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        FilterableAttributeList $filterableAttributes,
        \Magento\Search\Helper\Data $helper,
        array $filters = array()
    ) {
        $this->helper = $helper;
        if ($helper->getIsEngineAvailableForNavigation(false)) {
            $this->filters[self::CATEGORY_FILTER]  = 'Magento\Search\Model\Catalog\Layer\Filter\Category';
            $this->filters[self::ATTRIBUTE_FILTER] = 'Magento\Search\Model\Search\Layer\Filter\Attribute';
            $this->filters[self::PRICE_FILTER]     = 'Magento\Search\Model\Catalog\Layer\Filter\Price';
            $this->filters[self::DECIMAL_FILTER]   = 'Magento\Search\Model\Catalog\Layer\Filter\Decimal';
        }
        parent::__construct($objectManager, $filterableAttributes, $filters);
    }


    public function getFilters()
    {
        $filters = parent::getFilters();
        if ($this->helper->isThirdPartSearchEngine() && $this->helper->getIsEngineAvailableForNavigation(false)) {
            foreach ($filters as $filter) {
                $filter->addFacetCondition();
            }
        }
        return $filters;
    }
}

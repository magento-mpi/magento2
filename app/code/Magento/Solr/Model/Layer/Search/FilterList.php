<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Solr\Model\Layer\Search;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @var \Magento\Solr\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param FilterableAttributeList $filterableAttributes
     * @param \Magento\Solr\Helper\Data $helper
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        FilterableAttributeList $filterableAttributes,
        \Magento\Solr\Helper\Data $helper,
        array $filters = array()
    ) {
        $this->helper = $helper;
        if ($helper->getIsEngineAvailableForNavigation(false)) {
            $this->filterTypes[self::CATEGORY_FILTER]  = 'Magento\Solr\Model\Layer\Category\Filter\Category';
            $this->filterTypes[self::ATTRIBUTE_FILTER] = 'Magento\Solr\Model\Layer\Search\Filter\Attribute';
            $this->filterTypes[self::PRICE_FILTER]     = 'Magento\Solr\Model\Layer\Category\Filter\Price';
            $this->filterTypes[self::DECIMAL_FILTER]   = 'Magento\Solr\Model\Layer\Category\Filter\Decimal';
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
            if ($this->helper->isThirdPartSearchEngine() && $this->helper->getIsEngineAvailableForNavigation(false)) {
                foreach ($filters as $filter) {
                    $filter->addFacetCondition();
                }
            }
        }
        return $this->filters;
    }
}

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer;

use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;

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
        if (!$helper->getIsEngineAvailableForNavigation()) {
            $filters = array();
        }
        parent::__construct($objectManager, $filterableAttributes, $filters); // TODO: Change the autogenerated stub
    }

    public function getFilters()
    {
        $filters = parent::getFilters();
        if ($this->helper->isThirdPartSearchEngine() && $this->helper->getIsEngineAvailableForNavigation()) {
            foreach ($filters as $filter) {
                $filter->addFacetCondition();
            }
        }
        return $filters;
    }
}

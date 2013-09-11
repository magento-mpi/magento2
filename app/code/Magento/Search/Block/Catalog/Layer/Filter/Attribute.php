<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog attribute layer filter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Block\Catalog\Layer\Filter;

class Attribute extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = '\Magento\Search\Model\Catalog\Layer\Filter\Attribute';
    }

    /**
     * Set attribute model
     *
     * @return \Magento\Search\Block\Catalog\Layer\Filter\Attribute
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Search\Block\Catalog\Layer\Filter\Attribute
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }

    /**
     * Get filter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        $attributeIsFilterable = $this->getAttributeModel()->getIsFilterable();
        if ($attributeIsFilterable == \Magento\Catalog\Model\Layer\Filter\Attribute::OPTIONS_ONLY_WITH_RESULTS) {
            return parent::getItemsCount();
        }

        $count = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getCount()) {
                $count++;
            }
        }

        return $count;
    }
}

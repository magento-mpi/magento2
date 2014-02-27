<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Catalog\Layer\Filter;

/**
 * Catalog Layer Decimal Attribute Filter Block
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Decimal extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Initialize Decimal Filter Model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\Search\Model\Catalog\Layer\Filter\Decimal';
    }

    /**
     * Prepare filter process
     *
     * @return $this
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return $this
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

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
 * Catalog layer price filter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Block\Catalog\Layer\Filter;

class Price extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Initialize Price filter module
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\Search\Model\Catalog\Layer\Filter\Price';
    }

    /**
     * Prepare filter process
     *
     * @return \Magento\Catalog\Block\Layer\Filter\Price
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Search\Block\Catalog\Layer\Filter\Price
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

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
 * Catalog layer category filter
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Block\Catalog\Layer\Filter;

class Category extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = '\Magento\Search\Model\Catalog\Layer\Filter\Category';
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Search\Block\Catalog\Layer\Filter\Category
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

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
 * Catalog layer category filter
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Category extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Set model name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\Search\Model\Catalog\Layer\Filter\Category';
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

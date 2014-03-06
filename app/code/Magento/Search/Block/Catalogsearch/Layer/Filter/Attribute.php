<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Catalogsearch\Layer\Filter;

/**
 * Catalog attribute layer filter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Set model name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\Search\Model\Search\Layer\Filter\Attribute';
    }

    /**
     * Set attribute model
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

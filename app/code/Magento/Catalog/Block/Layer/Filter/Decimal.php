<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Layer\Filter;

/**
 * Catalog Layer Decimal Attribute Filter Block
 *
 * @category    Magento
 * @package     Magento_Catalog
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
        $this->_filterModelName = 'Magento\Catalog\Model\Layer\Filter\Decimal';
    }

    /**
     * Prepare filter process
     *
     * @return \Magento\Catalog\Block\Layer\Filter\Decimal
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}

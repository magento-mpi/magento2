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
 * Catalog layer price filter
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Price extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    /**
     * Initialize Price filter module
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_filterModelName = 'Magento\Catalog\Model\Layer\Filter\Price';
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
}

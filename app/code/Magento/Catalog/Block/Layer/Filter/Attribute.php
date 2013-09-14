<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog attribute layer filter
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Layer\Filter;

class Attribute extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\Catalog\Model\Layer\Filter\Attribute';
    }

    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}

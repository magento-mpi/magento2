<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch attribute layer filter
 *
 */
namespace Magento\CatalogSearch\Block\Layer\Filter;

class Attribute extends \Magento\Catalog\Block\Layer\Filter\Attribute
{
    /**
     * Set filter model name
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\CatalogSearch\Model\Layer\Filter\Attribute';
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Block\Layer\Filter;

/**
 * CatalogSearch attribute layer filter
 *
 */
class Attribute extends \Magento\Catalog\Block\Layer\Filter\Attribute
{
    /**
     * Set filter model name
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\CatalogSearch\Model\Layer\Filter\Attribute';
    }
}

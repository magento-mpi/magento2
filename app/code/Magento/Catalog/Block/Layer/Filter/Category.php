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
 * Catalog layer category filter
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Layer\Filter;

class Category extends \Magento\Catalog\Block\Layer\Filter\AbstractFilter
{
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento\Catalog\Model\Layer\Filter\Category';
    }
}

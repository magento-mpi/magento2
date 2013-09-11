<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Configurable product associated products in stock filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Grid\Filter;

class Inventory extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{

    protected function _getOptions()
    {
        return array(
            array(
                'value' =>  '',
                'label' =>  ''
            ),
            array(
                'value' =>  1,
                'label' =>  __('In Stock')
            ),
            array(
                'value' =>  0,
                'label' =>  __('Out of Stock')
            )
        );
    }

}

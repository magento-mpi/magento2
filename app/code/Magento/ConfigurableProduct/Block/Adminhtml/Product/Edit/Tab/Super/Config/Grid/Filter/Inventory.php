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
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config\Grid\Filter;

use Magento\Backend\Block\Widget\Grid\Column\Filter\Select;

class Inventory extends Select
{

    /**
     * @return array
     */
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

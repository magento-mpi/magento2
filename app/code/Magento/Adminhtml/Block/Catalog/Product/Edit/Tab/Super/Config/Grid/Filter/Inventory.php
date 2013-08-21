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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Filter_Inventory extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Select
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

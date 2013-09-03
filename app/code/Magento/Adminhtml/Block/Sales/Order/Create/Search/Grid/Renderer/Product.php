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
 * Adminhtml sales create order product search grid product name column renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Product extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $rendered       =  parent::render($row);
        $isConfigurable = $row->canConfigure();
        $style          = $isConfigurable ? '' : 'disabled';
        $prodAttributes = $isConfigurable ? sprintf('list_type = "product_to_add" product_id = %s', $row->getId()) : 'disabled="disabled"';
        return sprintf('<a href="javascript:void(0)" class="action-configure %s" %s>%s</a>',
            $style, $prodAttributes, __('Configure')) . $rendered;
    }
}

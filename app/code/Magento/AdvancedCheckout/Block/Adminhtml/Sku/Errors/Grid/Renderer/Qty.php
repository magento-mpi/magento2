<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Qty field renderer
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Grid_Renderer_Qty
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders qty column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $isDisabled = ($row->getCode() == Magento_AdvancedCheckout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU)
            || $row->getIsConfigureDisabled()
            || $row->getIsQtyDisabled();

        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '" ';
        $html .= $isDisabled ? 'disabled="disabled" ' : '';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}

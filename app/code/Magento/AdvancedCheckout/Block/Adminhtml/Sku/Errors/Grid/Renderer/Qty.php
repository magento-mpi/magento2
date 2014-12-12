<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Qty field renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\Renderer;

class Qty extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders qty column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $isDisabled = $row->getCode() == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU ||
            $row->getIsConfigureDisabled() ||
            $row->getIsQtyDisabled();

        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '" ';
        $html .= $isDisabled ? 'disabled="disabled" ' : '';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}

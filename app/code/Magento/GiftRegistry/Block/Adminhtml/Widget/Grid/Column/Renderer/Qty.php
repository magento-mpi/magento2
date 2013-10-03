<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for gift registry item grid qty column
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Qty
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render gift registry item qty as input html element
     *
     * @param  \Magento\Object $row
     * @return string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex()) * 1;

        $html = '<input type="text" ';
        $html .= 'name="items[' . $row->getItemId() . '][' . $this->getColumn()->getId() . ']"';
        $html .= 'value="' . $value . '"';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_Widget_Grid_Column_Renderer_DraggableHandle
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render grid row
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        return '<span class="' . $this->getColumn()->getInlineCss() . '"></span>'
            . '<input type="hidden" name="entity_id" value="' . $row->getData($this->getColumn()->getIndex()) . '"/>'
            . '<input type="hidden" name="position" value=""/>';
    }
}

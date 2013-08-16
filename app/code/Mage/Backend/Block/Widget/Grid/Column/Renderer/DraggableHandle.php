<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_Column_Renderer_DraggableHandle
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render grid row
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        return '<span class="' . $this->getColumn()->getInlineCss() . '"></span>'
            . '<input type="hidden" name="entity_id" value="' . $row->getData($this->getColumn()->getIndex()) . '"/>'
            . '<input type="hidden" name="position" value=""/>';
    }
}

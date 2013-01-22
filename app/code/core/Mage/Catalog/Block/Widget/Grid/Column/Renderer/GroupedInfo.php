<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Widget_Grid_Column_Renderer_GroupedInfo
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render grid row
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $html = '<span class="ui-icon ui-icon-grip-dotted-vertical"></span>';
        $html .= '<input type="hidden" name="entity_id" value="' . $row->getData($this->getColumn()->getIndex()) . '"/>';
        $html .= '<input type="hidden" name="position" value=""/>';
        return $html;
    }
}

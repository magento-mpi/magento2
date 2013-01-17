<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Adminhtml_Grid_Column_Renderer_Button
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
        $label = $this->getColumn()->getHeader();
        $enityId = $row->getData('entity_id');
        return '<button data-entity-id="' . $enityId . '">' . $label . '</button>';
    }
}

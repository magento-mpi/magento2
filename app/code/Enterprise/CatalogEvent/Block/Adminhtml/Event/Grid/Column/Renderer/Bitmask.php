<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Events grid bitmask renderer
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid_Column_Renderer_Bitmask
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Magento_Object $row)
    {
        $value = (int) $row->getData($this->getColumn()->getIndex());
        $result = array();
        foreach ($this->getColumn()->getOptions() as $option => $label) {
            if (($value & $option) == $option) {
                $result[] = $label;
            }
        }

        return $this->escapeHtml(implode(', ', $result));
    }
}

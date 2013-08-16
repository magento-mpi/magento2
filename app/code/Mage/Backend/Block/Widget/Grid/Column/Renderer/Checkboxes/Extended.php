<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Checkboxes_Extended
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Checkbox
{
    /**
     * Prepare data for renderer
     *
     * @return array
     */
    public function _getValues()
    {
        return $this->getColumn()->getValues();
    }
}

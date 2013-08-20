<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Block_Widget_Grid_Column_Extended extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Mage_Backend_Block_Template_Context $context, array $data = array())
    {
        $this->_rendererTypes['options'] = 'Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Extended';
        $this->_filterTypes['options'] = 'Mage_Backend_Block_Widget_Grid_Column_Filter_Select_Extended';
        $this->_rendererTypes['select'] = 'Mage_Backend_Block_Widget_Grid_Column_Renderer_Select_Extended';
        $this->_rendererTypes['checkbox'] = 'Mage_Backend_Block_Widget_Grid_Column_Renderer_Checkboxes_Extended';
        $this->_rendererTypes['radio'] = 'Mage_Backend_Block_Widget_Grid_Column_Renderer_Radio_Extended';

        parent::__construct($context, $data);
    }
}
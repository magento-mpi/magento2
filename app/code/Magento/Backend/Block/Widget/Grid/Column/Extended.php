<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Block_Widget_Grid_Column_Extended extends Magento_Backend_Block_Widget_Grid_Column
{
    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        $this->_rendererTypes['options'] = 'Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Extended';
        $this->_filterTypes['options'] = 'Magento_Backend_Block_Widget_Grid_Column_Filter_Select_Extended';
        $this->_rendererTypes['select'] = 'Magento_Backend_Block_Widget_Grid_Column_Renderer_Select_Extended';
        $this->_rendererTypes['checkbox'] = 'Magento_Backend_Block_Widget_Grid_Column_Renderer_Checkboxes_Extended';
        $this->_rendererTypes['radio'] = 'Magento_Backend_Block_Widget_Grid_Column_Renderer_Radio_Extended';

        parent::__construct($context, $data);
    }
}

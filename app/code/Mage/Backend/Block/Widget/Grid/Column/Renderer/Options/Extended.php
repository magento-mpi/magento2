<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Extended
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter
     */
    protected $_converter;

    /**
     * @param Mage_Backend_Block_Context $context
     * @param Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Context $context,
        Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_converter = $converter;
    }

    /**
     * Prepare data for renderer
     *
     * @return array
     */
    public function _getOptions()
    {
        $options = $this->getColumn()->getOptions();
        return $this->_converter->toTreeArray($options);
    }
}
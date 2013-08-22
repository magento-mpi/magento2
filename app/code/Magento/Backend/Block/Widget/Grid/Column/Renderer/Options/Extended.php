<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Extended
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter
     */
    protected $_converter;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter,
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

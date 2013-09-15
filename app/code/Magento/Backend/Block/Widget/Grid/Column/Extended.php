<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column;

class Extended extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rendererTypes['options'] = 'Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Extended';
        $this->_filterTypes['options'] = 'Magento\Backend\Block\Widget\Grid\Column\Filter\Select\Extended';
        $this->_rendererTypes['select'] = 'Magento\Backend\Block\Widget\Grid\Column\Renderer\Select\Extended';
        $this->_rendererTypes['checkbox'] = 'Magento\Backend\Block\Widget\Grid\Column\Renderer\Checkboxes\Extended';
        $this->_rendererTypes['radio'] = 'Magento\Backend\Block\Widget\Grid\Column\Renderer\Radio\Extended';

        parent::__construct($coreData, $context, $data);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column;

class Extended extends \Magento\Ui\Listing\Block\Column
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = array())
    {
        $this->_rendererTypes['options'] = 'Magento\Ui\Listing\Block\Column\Renderer\Options\Extended';
        $this->_filterTypes['options'] = 'Magento\Ui\Listing\Block\Column\Filter\Select\Extended';
        $this->_rendererTypes['select'] = 'Magento\Ui\Listing\Block\Column\Renderer\Select\Extended';
        $this->_rendererTypes['checkbox'] = 'Magento\Ui\Listing\Block\Column\Renderer\Checkboxes\Extended';
        $this->_rendererTypes['radio'] = 'Magento\Ui\Listing\Block\Column\Renderer\Radio\Extended';

        parent::__construct($context, $data);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Renderer\Options;

class Extended extends \Magento\Ui\Listing\Block\Column\Renderer\Options
{
    /**
     * @var \Magento\Ui\Listing\Block\Column\Renderer\Options\Converter
     */
    protected $_converter;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Ui\Listing\Block\Column\Renderer\Options\Converter $converter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Ui\Listing\Block\Column\Renderer\Options\Converter $converter,
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report\Massaction;

class Set
    implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\CustomerSegment\Helper\Data
     */
    protected $_segmentHelper;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    protected $_dataConverter;

    /**
     * @param \Magento\CustomerSegment\Helper\Data $helper
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     */
    public function __construct(
        \Magento\CustomerSegment\Helper\Data $helper,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
    ) {
        $this->_segmentHelper = $helper;
        $this->_dataConverter = $converter;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_dataConverter->toFlatArray($this->_segmentHelper->getOptionsArray());
    }
}

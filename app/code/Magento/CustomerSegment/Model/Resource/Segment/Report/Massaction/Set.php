<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerSegment_Model_Resource_Segment_Report_Massaction_Set
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_CustomerSegment_Helper_Data
     */
    protected $_segmentHelper;

    /**
     * @var Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter
     */
    protected $_dataConverter;

    /**
     * @param Magento_CustomerSegment_Helper_Data $helper
     * @param Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
     */
    public function __construct(
        Magento_CustomerSegment_Helper_Data $helper,
        Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
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

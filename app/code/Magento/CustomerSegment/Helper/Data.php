<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * XPath where customer segment's on/off status is stored
     */
    const XML_PATH_CUSTOMER_SEGMENT_ENABLER = 'customer/magento_customersegment/is_enabled';

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    private $_storeConfig;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment\Collection
     */
    private $_segmentCollection;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\CustomerSegment\Model\Resource\Segment\Collection $segmentCollection
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\CustomerSegment\Model\Resource\Segment\Collection $segmentCollection
    ) {
        parent::__construct($context);
        $this->_storeConfig = $storeConfig;
        $this->_segmentCollection = $segmentCollection;
    }

    /**
     * Check whether customer segment functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_storeConfig->getConfig(self::XML_PATH_CUSTOMER_SEGMENT_ENABLER);
    }

    /**
     * Retrieve options array for customer segment view mode
     *
     * @return array
     */
    public function getOptionsArray()
    {
        return array(
            array(
                'label' => '',
                'value' => ''
            ),
            array(
                'label' => __('Union'),
                'value' => \Magento\CustomerSegment\Model\Segment::VIEW_MODE_UNION_CODE
            ),
            array(
                'label' => __('Intersection'),
                'value' => \Magento\CustomerSegment\Model\Segment::VIEW_MODE_INTERSECT_CODE
            )
        );
    }

    /**
     * Return translated Label for option by specified option code
     *
     * @param string $code Option code
     * @return string
     */
    public function getViewModeLabel($code)
    {
        foreach ($this->getOptionsArray() as $option) {
            if (isset($option['label']) && isset($option['value']) && $option['value'] == $code) {
                return $option['label'];
            }
        }
        return '';
    }

    /**
     * Add customer segment fields to a form and its data
     *
     * @param \Magento\Data\Form $form
     * @param \Magento\Object $formData
     * @param \Magento\Backend\Block\Widget\Form\Element\Dependence $fieldDependencies
     */
    public function addSegmentFieldsToForm(
        \Magento\Data\Form $form,
        \Magento\Object $formData,
        \Magento\Backend\Block\Widget\Form\Element\Dependence $fieldDependencies
    ) {
        if (!$this->isEnabled()) {
            return;
        }

        $formData->setUseCustomerSegment(count($formData->getCustomerSegmentIds()) > 0);

        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /** @var \Magento\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField('use_customer_segment', 'select', array(
            'name' => 'use_customer_segment',
            'label' => __('Customer Segments'),
            'options' => array(
                '0' => __('All'),
                '1' => __('Specified'),
            ),
            'note' => $formData->getUseCustomerSegment()
                ? $this->_getSpecificSegmentMessage()  : $this->_getAllSegmentsMessage(),
            'disabled' => $formData->getIsReadonly(),
            'after_element_html' => $this->_getChangeNoteMessageScript(
                "{$htmlIdPrefix}use_customer_segment",
                'use_customer_segment-note'
            )
        ));

        $fieldset->addField('customer_segment_ids', 'multiselect', array(
            'name' => 'customer_segment_ids',
            'values' => $this->_segmentCollection->toOptionArray(),
            'required' => true,
            'can_be_empty' => true,
        ));

        $fieldDependencies
            ->addFieldMap("{$htmlIdPrefix}use_customer_segment", 'use_customer_segment')
            ->addFieldMap("{$htmlIdPrefix}customer_segment_ids", 'customer_segment_ids')
            ->addFieldDependence('customer_segment_ids', 'use_customer_segment', '1');
    }

    /**
     * Retrieve JavaScript that actualizes customer segment field's note message upon changes in run-time
     *
     * @param string $selectBoxId
     * @param string $noteMessageBlockId
     * @return string
     */
    protected function _getChangeNoteMessageScript($selectBoxId, $noteMessageBlockId)
    {
        $allSegmentsMsg = $this->_getAllSegmentsMessage();
        $specificSegmentMsg = $this->_getSpecificSegmentMessage();
        return "<script type=\"text/javascript\">\r\n"
            . "(function($) {\r\n"
            . "'use strict';\r\n"
            . "var notes = [\"{$allSegmentsMsg}\", \"{$specificSegmentMsg}\"];\r\n"
            . "\$('#$selectBoxId').change(function() {\r\n"
            . "var note = notes[\$('#$selectBoxId').val()];\r\n"
            . "\$('#$noteMessageBlockId').html(note);\r\n"
            . "});\r\n"
            . "})(jQuery);\r\n"
            . "</script>\r\n";
    }

    /**
     * Retrieve translated "apply to all segments" message
     *
     * @return string
     */
    protected function _getAllSegmentsMessage()
    {
        return __('Applies to All of the Specified Customer Segments');
    }

    /**
     * Retrieve translated "apply to specific segment" message
     *
     * @return string
     */
    protected function _getSpecificSegmentMessage()
    {
        return __('Apply to the Selected Customer Segments');
    }
}

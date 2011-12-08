<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerSegment_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check whether customer segment functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig('customer/enterprise_customersegment/is_enabled');
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
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Union'),
                'value' => Enterprise_CustomerSegment_Model_Segment::VIEW_MODE_UNION_CODE
            ),
            array(
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Intersection'),
                'value' => Enterprise_CustomerSegment_Model_Segment::VIEW_MODE_INTERSECT_CODE
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
}

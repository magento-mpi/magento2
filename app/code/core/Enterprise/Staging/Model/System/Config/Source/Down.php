<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Options for staging frontend blocking
 *
 */
class Enterprise_Staging_Model_System_Config_Source_Down extends Varien_Object
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('Enterprise_Staging_Helper_Data')->__('No'),
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Close Entire Frontend'),
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Close only relevant websites'),
            ),
        );
    }
}

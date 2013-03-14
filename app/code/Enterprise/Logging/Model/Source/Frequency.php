<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
  * Source model for logging frequency
  */
class Enterprise_Logging_Model_Source_Frequency
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('Enterprise_Logging_Helper_Data')->__('Daily')
            ),
            array(
                'value' => 7,
                'label' => Mage::helper('Enterprise_Logging_Helper_Data')->__('Weekly')
            ),
            array(
                'value' => 30,
                'label' => Mage::helper('Enterprise_Logging_Helper_Data')->__('Monthly')
            ),
        );
    }
}

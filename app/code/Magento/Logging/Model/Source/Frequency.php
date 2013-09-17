<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
  * Source model for logging frequency
  */
class Magento_Logging_Model_Source_Frequency implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => __('Daily')
            ),
            array(
                'value' => 7,
                'label' => __('Weekly')
            ),
            array(
                'value' => 30,
                'label' => __('Monthly')
            ),
        );
    }
}

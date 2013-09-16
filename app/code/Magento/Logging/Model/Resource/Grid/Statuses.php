<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Logging_Model_Resource_Grid_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            Magento_Logging_Model_Event::RESULT_SUCCESS => __('Success'),
            Magento_Logging_Model_Event::RESULT_FAILURE => __('Failure'),
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Logging_Model_Resource_Grid_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            Enterprise_Logging_Model_Event::RESULT_SUCCESS => __('Success'),
            Enterprise_Logging_Model_Event::RESULT_FAILURE => __('Failure'),
        );
    }
}

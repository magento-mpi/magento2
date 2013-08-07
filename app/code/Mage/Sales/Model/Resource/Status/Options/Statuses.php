<?php
/**
 * Order statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_Resource_Status_Options_Statuses implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0' => __('No'),
            '1' => __('Yes'),
        );
    }
}

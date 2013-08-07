<?php
/**
 * User statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_SalesRule_Model_Resource_Rule_Quote_StatusesArray implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => __('Active'),
            '0' => __('Inactive'),
        );
    }
}

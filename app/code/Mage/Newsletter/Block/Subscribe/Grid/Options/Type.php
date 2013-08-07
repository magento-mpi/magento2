<?php
/**
 * Newsletter grid type options
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Newsletter_Block_Subscribe_Grid_Options_Type implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return column options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => __('Guest'),
            '2' => __('Customer'),
        );
    }
}

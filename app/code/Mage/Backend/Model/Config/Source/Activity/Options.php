<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Source_Activity_Options
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return options
     *
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



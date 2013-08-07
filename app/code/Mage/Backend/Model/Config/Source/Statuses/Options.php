<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Source_Statuses_Options
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array('0' => __('Disabled'),
            '1' => __('Enabled'));
    }
}

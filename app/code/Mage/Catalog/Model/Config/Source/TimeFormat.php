<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Config_Source_TimeFormat implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => '12h', 'label' => __('12h AM/PM')),
            array('value' => '24h', 'label' => __('24h')),
        );
    }
}

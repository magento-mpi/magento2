<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Dev_Dbautoup implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value'=>Mage_Core_Model_Resource::AUTO_UPDATE_ALWAYS,
                'label' => __('Always (during development)')
            ),
            array(
                'value'=>Mage_Core_Model_Resource::AUTO_UPDATE_ONCE,
                'label' => __('Only Once (version upgrade)')
            ),
            array(
                'value'=>Mage_Core_Model_Resource::AUTO_UPDATE_NEVER,
                'label' => __('Never (production)')
            ),
        );
    }

}

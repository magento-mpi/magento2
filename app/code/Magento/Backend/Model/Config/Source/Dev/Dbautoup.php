<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Dev_Dbautoup implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value'=>Magento_Core_Model_Resource::AUTO_UPDATE_ALWAYS,
                'label' => __('Always (during development)')
            ),
            array(
                'value'=>Magento_Core_Model_Resource::AUTO_UPDATE_ONCE,
                'label' => __('Only Once (version upgrade)')
            ),
            array(
                'value'=>Magento_Core_Model_Resource::AUTO_UPDATE_NEVER,
                'label' => __('Never (production)')
            ),
        );
    }

}

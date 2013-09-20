<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'text', 'label' => __('Text Field')),
            array('value' => 'textarea', 'label' => __('Text Area')),
            array('value' => 'date', 'label' => __('Date')),
            array('value' => 'boolean', 'label' => __('Yes/No')),
            array('value' => 'multiselect', 'label' => __('Multiple Select')),
            array('value' => 'select', 'label' => __('Dropdown'))
        );
    }
}

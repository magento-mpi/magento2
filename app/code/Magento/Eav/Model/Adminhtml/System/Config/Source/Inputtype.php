<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Adminhtml\System\Config\Source;

class Inputtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options
     *
     * @return array
     */
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

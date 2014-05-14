<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\System\Config\Source;

class Inputtype
{
    /**
     * Get input types which use predefined source
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'multiselect', 'label' => __('Multiple Select')),
            array('value' => 'select', 'label' => __('Dropdown'))
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source;

class Enabledisable implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(array('value' => 1, 'label' => __('Enable')), array('value' => 0, 'label' => __('Disable')));
    }
}

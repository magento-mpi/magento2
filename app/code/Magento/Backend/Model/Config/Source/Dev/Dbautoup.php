<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Dev;

class Dbautoup implements \Magento\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value'=> \Magento\App\Resource::AUTO_UPDATE_ALWAYS,
                'label' => __('Always (during development)')
            ),
            array(
                'value'=> \Magento\App\Resource::AUTO_UPDATE_ONCE,
                'label' => __('Only Once (version upgrade)')
            ),
            array(
                'value'=> \Magento\App\Resource::AUTO_UPDATE_NEVER,
                'label' => __('Never (production)')
            ),
        );
    }

}

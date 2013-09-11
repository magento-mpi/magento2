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

class Dbautoup implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value'=>\Magento\Core\Model\Resource::AUTO_UPDATE_ALWAYS,
                'label' => __('Always (during development)')
            ),
            array(
                'value'=>\Magento\Core\Model\Resource::AUTO_UPDATE_ONCE,
                'label' => __('Only Once (version upgrade)')
            ),
            array(
                'value'=>\Magento\Core\Model\Resource::AUTO_UPDATE_NEVER,
                'label' => __('Never (production)')
            ),
        );
    }

}

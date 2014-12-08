<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone template Action Dropdown source
 */
namespace Magento\Ogone\Model\Source;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Prepare ogone template mode list as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \Magento\Ogone\Model\Api::TEMPLATE_OGONE, 'label' => __('Ogone')],
            ['value' => \Magento\Ogone\Model\Api::TEMPLATE_MAGENTO, 'label' => __('Magento')]
        ];
    }
}

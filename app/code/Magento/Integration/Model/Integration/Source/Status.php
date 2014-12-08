<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Integration\Source;

/**
 * Integration status options.
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \Magento\Integration\Model\Integration::STATUS_INACTIVE, 'label' => __('Inactive')],
            ['value' => \Magento\Integration\Model\Integration::STATUS_ACTIVE, 'label' => __('Active')]
        ];
    }
}

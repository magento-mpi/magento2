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
class Status implements \Magento\Option\ArrayInterface
{
    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Integration\Model\Integration::STATUS_INACTIVE,
                'label' => __('Inactive')
            ),
            array(
                'value' => \Magento\Integration\Model\Integration::STATUS_ACTIVE,
                'label' => __('Active')
            ),
        );
    }
}

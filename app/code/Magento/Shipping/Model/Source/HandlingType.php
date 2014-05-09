<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Source;

class HandlingType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Shipping\Model\Carrier\AbstractCarrier::HANDLING_TYPE_FIXED,
                'label' => __('Fixed')
            ),
            array(
                'value' => \Magento\Shipping\Model\Carrier\AbstractCarrier::HANDLING_TYPE_PERCENT,
                'label' => __('Percent')
            )
        );
    }
}

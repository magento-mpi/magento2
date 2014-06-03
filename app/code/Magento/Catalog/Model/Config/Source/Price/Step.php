<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Price;

class Step implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Catalog\Model\Layer\Filter\Price::RANGE_CALCULATION_AUTO,
                'label' => __('Automatic (equalize price ranges)')
            ),
            array(
                'value' => \Magento\Catalog\Model\Layer\Filter\Price::RANGE_CALCULATION_IMPROVED,
                'label' => __('Automatic (equalize product counts)')
            ),
            array(
                'value' => \Magento\Catalog\Model\Layer\Filter\Price::RANGE_CALCULATION_MANUAL,
                'label' => __('Manual')
            )
        );
    }
}

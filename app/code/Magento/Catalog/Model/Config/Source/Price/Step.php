<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Price;

use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Framework\Option\ArrayInterface;

class Step implements ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => AlgorithmFactory::RANGE_CALCULATION_AUTO,
                'label' => __('Automatic (equalize price ranges)')
            ),
            array(
                'value' => AlgorithmFactory::RANGE_CALCULATION_IMPROVED,
                'label' => __('Automatic (equalize product counts)')
            ),
            array(
                'value' => AlgorithmFactory::RANGE_CALCULATION_MANUAL,
                'label' => __('Manual')
            )
        );
    }
}

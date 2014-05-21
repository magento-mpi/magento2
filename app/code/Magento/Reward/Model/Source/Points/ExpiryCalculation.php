<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Source\Points;

/**
 * Source model for list of Expiry Calculation algorithms
 */
class ExpiryCalculation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Expiry calculation options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'static', 'label' => __('Static')),
            array('value' => 'dynamic', 'label' => __('Dynamic'))
        );
    }
}

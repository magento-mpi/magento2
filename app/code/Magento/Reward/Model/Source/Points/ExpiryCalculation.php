<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        return [
            ['value' => 'static', 'label' => __('Static')],
            ['value' => 'dynamic', 'label' => __('Dynamic')]
        ];
    }
}

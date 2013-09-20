<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source model for list of Expiry Calculation algorythms
 */
class Magento_Reward_Model_Source_Points_ExpiryCalculation implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'static', 'label' => __('Static')),
            array('value' => 'dynamic', 'label' => __('Dynamic')),
        );
    }
}

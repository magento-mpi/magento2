<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source model for list of Expiry Calculation algorythms
 */
class Enterprise_Reward_Model_Source_Points_ExpiryCalculation
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'static', 'label' => __('Static')),
            array('value' => 'dynamic', 'label' => __('Dynamic')),
        );
    }
}

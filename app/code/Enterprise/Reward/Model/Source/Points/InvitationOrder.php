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
 * Source model for Acquiring frequency when Order processed after Invitation
 */
class Enterprise_Reward_Model_Source_Points_InvitationOrder
{
    public function toOptionArray()
    {
        return array(
            array('value' => '*', 'label' => __('Each')),
            array('value' => '1', 'label' => __('First')),
        );
    }
}

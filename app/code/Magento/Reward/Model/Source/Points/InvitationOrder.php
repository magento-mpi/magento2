<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Source\Points;

/**
 * Source model for Acquiring frequency when Order processed after Invitation
 */
class InvitationOrder implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Invitation order options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(array('value' => '*', 'label' => __('Each')), array('value' => '1', 'label' => __('First')));
    }
}

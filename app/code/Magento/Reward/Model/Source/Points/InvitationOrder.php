<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        return [['value' => '*', 'label' => __('Each')], ['value' => '1', 'label' => __('First')]];
    }
}

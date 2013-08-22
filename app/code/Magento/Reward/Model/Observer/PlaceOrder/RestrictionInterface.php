<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Reward_Model_Observer_PlaceOrder_RestrictionInterface
{
    /**
     * Check if reward points operations is allowed
     *
     * @return bool
     */
    public function isAllowed();
}
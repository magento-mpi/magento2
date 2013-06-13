<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Model_Observer_PlaceOrder_Restriction_Frontend
    implements Enterprise_Reward_Model_Observer_PlaceOrder_RestrictionInterface
{
    /**
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_Reward_Helper_Data $helper
     */
    public function __construct(Enterprise_Reward_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Check if reward points operations is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_helper->isEnabledOnFront();
    }
}
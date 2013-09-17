<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Model_Observer_PlaceOrder_Restriction_Frontend
    implements Magento_Reward_Model_Observer_PlaceOrder_RestrictionInterface
{
    /**
     * @var Magento_Reward_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Reward_Helper_Data $helper
     */
    public function __construct(Magento_Reward_Helper_Data $helper)
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
<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Model_Observer_PlaceOrder_Restriction_Backend
    implements Magento_Reward_Model_Observer_PlaceOrder_RestrictionInterface
{
    /**
     * @var Magento_Reward_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Reward_Helper_Data $helper
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Magento_Reward_Helper_Data $helper,
        Magento_AuthorizationInterface $authorization
    ) {
        $this->_helper = $helper;
        $this->_authorization = $authorization;
    }

    /**
     * Check if reward points operations is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_helper->isEnabledOnFront()
            && $this->_authorization->isAllowed(Magento_Reward_Helper_Data::XML_PATH_PERMISSION_AFFECT);
    }
}
<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Model_Observer_PlaceOrder_Restriction_Backend
    implements Enterprise_Reward_Model_Observer_PlaceOrder_RestrictionInterface
{
    /**
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Enterprise_Reward_Helper_Data $helper
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Enterprise_Reward_Helper_Data $helper,
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
            && $this->_authorization->isAllowed(Enterprise_Reward_Helper_Data::XML_PATH_PERMISSION_AFFECT);
    }
}
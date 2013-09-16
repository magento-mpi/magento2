<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Invitation_Model_Plugin_CustomerRegistration
{
    /**
     * @var Magento_Invitation_Model_Config
     */
    protected $_invitationConfig;

    /**
     * @var Magento_Invitation_Helper_Data
     */
    protected $_invitationHelper;

    /**
     * @param Magento_Invitation_Model_Config $invitationConfig
     * @param Magento_Invitation_Helper_Data $invitationHelper
     */
    public function __construct(
        Magento_Invitation_Model_Config $invitationConfig,
        Magento_Invitation_Helper_Data $invitationHelper
    ) {
        $this->_invitationConfig = $invitationConfig;
        $this->_invitationHelper = $invitationHelper;
    }

    /**
     * Check if registration is allowed
     *
     * @param boolean $invocationResult
     * @return boolean
     */
    public function afterIsRegistrationAllowed($invocationResult)
    {
        if (!$this->_invitationConfig->isEnabledOnFront()) {
            return $invocationResult;
        }

        if (!$invocationResult) {
            $this->_invitationHelper->isRegistrationAllowed(false);
        } else {
            $this->_invitationHelper->isRegistrationAllowed(true);
            $invocationResult = !$this->_invitationConfig->getInvitationRequired();
        }
        return $invocationResult;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Invitation\Model\Plugin;

class CustomerRegistration
{
    /**
     * @var \Magento\Invitation\Model\Config
     */
    protected $_invitationConfig;

    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationHelper;

    /**
     * @param \Magento\Invitation\Model\Config $invitationConfig
     * @param \Magento\Invitation\Helper\Data $invitationHelper
     */
    public function __construct(
        \Magento\Invitation\Model\Config $invitationConfig,
        \Magento\Invitation\Helper\Data $invitationHelper
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
    public function afterIsRegistrationAllowed(\Magento\Customer\Helper\Data $subject, $invocationResult)
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

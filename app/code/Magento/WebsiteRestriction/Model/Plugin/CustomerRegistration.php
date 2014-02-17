<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Plugin;

class CustomerRegistration
{
    /**
     * @var \Magento\WebsiteRestriction\Model\ConfigInterface
     */
    protected $_restrictionConfig;

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $restrictionConfig
     */
    public function __construct(\Magento\WebsiteRestriction\Model\ConfigInterface $restrictionConfig)
    {
        $this->_restrictionConfig = $restrictionConfig;
    }

    /**
     * Check if registration is allowed
     *
     * @param boolean $invocationResult
     * @return boolean
     */
    public function afterIsRegistrationAllowed(\Magento\Customer\Helper\Data $subject, $invocationResult)
    {
        if ($invocationResult) {
            $invocationResult = (!$this->_restrictionConfig->isRestrictionEnabled())
                || (\Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER === $this->_restrictionConfig->getMode());
        }
        return $invocationResult;
    }
}

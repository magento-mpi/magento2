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
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $restrictionConfig
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\WebsiteRestriction\Model\ConfigInterface $restrictionConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_restrictionConfig = $restrictionConfig;
    }

    /**
     * Check if registration is allowed
     *
     * @param boolean $invocationResult
     * @return boolean
     */
    public function afterIsRegistrationAllowed($invocationResult)
    {
        $currentStore = $this->_storeManager->getStore();
        if ((!$currentStore->isAdmin()) && $invocationResult) {
            $invocationResult = (!$this->_restrictionConfig->isRestrictionEnabled())
                || (\Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER === $this->_restrictionConfig->getMode());
        }
        return $invocationResult;
    }
}

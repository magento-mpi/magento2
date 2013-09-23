<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_WebsiteRestriction_Model_Plugin_CustomerRegistration
{
    /**
     * @var Magento_WebsiteRestriction_Model_ConfigInterface
     */
    protected $_restrictionConfig;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_WebsiteRestriction_Model_ConfigInterface $restrictionConfig
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_WebsiteRestriction_Model_ConfigInterface $restrictionConfig
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
                || (Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER === $this->_restrictionConfig->getMode());
        }
        return $invocationResult;
    }
}

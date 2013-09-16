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
     * @var Magento_WebsiteRestriction_Helper_Data
     */
    protected $_restrictionHelper;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_WebsiteRestriction_Helper_Data $restrictionHelper
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_WebsiteRestriction_Helper_Data $restrictionHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_restrictionHelper = $restrictionHelper;
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
            $restrictionMode = (int)$currentStore->getConfig(
                Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE
            );
            $invocationResult = (!$this->_restrictionHelper->getIsRestrictionEnabled())
                || (Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER === $restrictionMode);
        }
        return $invocationResult;
    }
}

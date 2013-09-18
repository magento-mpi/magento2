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
     * @var \Magento\WebsiteRestriction\Helper\Data
     */
    protected $_restrictionHelper;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\WebsiteRestriction\Helper\Data $restrictionHelper
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\WebsiteRestriction\Helper\Data $restrictionHelper
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
                \Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_MODE
            );
            $invocationResult = (!$this->_restrictionHelper->getIsRestrictionEnabled())
                || (\Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER === $restrictionMode);
        }
        return $invocationResult;
    }
}

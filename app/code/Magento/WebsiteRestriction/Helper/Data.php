<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WebsiteRestriction helper for translations
 *
 */
class Magento_WebsiteRestriction_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Website restriction settings
     */
    const XML_PATH_RESTRICTION_ENABLED            = 'general/restriction/is_active';
    const XML_PATH_RESTRICTION_MODE               = 'general/restriction/mode';
    const XML_PATH_RESTRICTION_LANDING_PAGE       = 'general/restriction/cms_page';
    const XML_PATH_RESTRICTION_HTTP_STATUS        = 'general/restriction/http_status';
    const XML_PATH_RESTRICTION_HTTP_REDIRECT      = 'general/restriction/http_redirect';
    const XML_NODE_RESTRICTION_ALLOWED_GENERIC    = 'frontend/magento/websiterestriction/full_action_names/generic';
    const XML_NODE_RESTRICTION_ALLOWED_REGISTER   = 'frontend/magento/websiterestriction/full_action_names/register';
    
    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_ConfigInterface $storeConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_ConfigInterface $storeConfig
    ) {
        $this->_storeConfig = $storeConfig;
        parent::__construct($context);
    }

    /**
     * Define if restriction is active
     *
     * @param Magento_Core_Model_Store|string|int $store
     * @return bool
     */
    public function getIsRestrictionEnabled($store = null)
    {
        return (bool)(int)$this->_storeConfig->getConfig(self::XML_PATH_RESTRICTION_ENABLED, $store);
    }
}

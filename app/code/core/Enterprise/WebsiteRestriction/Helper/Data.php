<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WebsiteRestriction helper for translations
 *
 */
class Enterprise_WebsiteRestriction_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Website restriction settings
     */
    const XML_PATH_RESTRICTION_ENABLED            = 'general/restriction/is_active';
    const XML_PATH_RESTRICTION_MODE               = 'general/restriction/mode';
    const XML_PATH_RESTRICTION_LANDING_PAGE       = 'general/restriction/cms_page';
    const XML_PATH_RESTRICTION_HTTP_STATUS        = 'general/restriction/http_status';
    const XML_PATH_RESTRICTION_HTTP_REDIRECT      = 'general/restriction/http_redirect';
    const XML_NODE_RESTRICTION_ALLOWED_GENERIC    = 'frontend/enterprise/websiterestriction/full_action_names/generic';
    const XML_NODE_RESTRICTION_ALLOWED_REGISTER   = 'frontend/enterprise/websiterestriction/full_action_names/register';

    /**
     * Define if restriction is active
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    public function getIsRestrictionEnabled($store = null)
    {
        return (bool)(int)Mage::getStoreConfig(self::XML_PATH_RESTRICTION_ENABLED, $store);
    }
}

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
namespace Magento\WebsiteRestriction\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
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
     * Define if restriction is active
     *
     * @param \Magento\Core\Model\Store|string|int $store
     * @return bool
     */
    public function getIsRestrictionEnabled($store = null)
    {
        return (bool)(int)\Mage::getStoreConfig(self::XML_PATH_RESTRICTION_ENABLED, $store);
    }
}

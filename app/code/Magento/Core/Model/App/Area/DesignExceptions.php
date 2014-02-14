<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\App\Area;

/**
 * Class DesignExceptions
 * @package Magento\Core\Model\App\Area
 */
class DesignExceptions
{
    /**
     * Design exception key
     */
    const XML_PATH_DESIGN_EXCEPTION = 'design/package/ua_regexp';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(\Magento\Core\Model\Store\Config $coreStoreConfig)
    {
        $this->coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Get theme that should be applied for current user-agent according to design exceptions configuration
     *
     * @param \Magento\App\Request\Http $request
     * @return string|bool
     */
    public function getThemeForUserAgent(\Magento\App\Request\Http $request)
    {
        $userAgent = $request->getServer('HTTP_USER_AGENT');
        if (empty($userAgent)) {
            return false;
        }
        $expressions = $this->coreStoreConfig->getConfig(self::XML_PATH_DESIGN_EXCEPTION);
        if (!$expressions) {
            return false;
        }
        $expressions = unserialize($expressions);
        foreach ($expressions as $rule) {
            if (preg_match($rule['regexp'], $userAgent)) {
                return $rule['value'];
            }
        }
        return false;
    }
}

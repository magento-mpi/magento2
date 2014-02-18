<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\View\Design\ThemeInterface;
use Magento\UrlInterface;

/**
 * Builds URLs for publicly accessible files
 */
class Url
{
    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * @var Service
     */
    private $service;

    /**
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    public function __construct(Service $service, \Magento\UrlInterface $baseUrl)
    {
        $this->service = $service;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Retrieve view file URL
     *
     * Get URL to file base on theme file identifier.
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = array())
    {
        list($module, $filePath) = Service::extractModule($fileId);
        $this->service->updateDesignParams($params);
        $relPath = self::getPathUsingTheme($filePath, $params['area'], $params['themeModel'], $params['locale'], $module);
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecure));
        return $baseUrl . $relPath;
    }

    /**
     * Build a fully qualified path to view file using theme object and other components
     *
     * @param string $filePath
     * @param string $areaCode
     * @param ThemeInterface $theme
     * @param string $localeCode
     * @param string $module
     * @return string
     */
    public static function getPathUsingTheme($filePath, $areaCode, ThemeInterface $theme, $localeCode, $module = '')
    {
        $themePath = $theme->getThemePath();
        if (!$themePath) {
            $themeId = $theme->getId();
            if ($themeId) {
                $themePath = self::PUBLIC_THEME_DIR . $themeId;
            } else {
                $themePath = self::PUBLIC_VIEW_DIR;
            }
        }
        return self::getFullyQualifiedPath($filePath, $areaCode, $themePath, $localeCode, $module);
    }

    /**
     * Build a fully qualified path to view file using specified components
     *
     * @param string $filePath
     * @param string $areaCode
     * @param string $themePath
     * @param string $localeCode
     * @param string $module
     * @return string
     */
    public static function getFullyQualifiedPath($filePath, $areaCode, $themePath, $localeCode, $module = '')
    {
        return $areaCode . '/' . $themePath . '/' . $localeCode . ($module ? '/' . $module : '') . '/' . $filePath;
    }
}

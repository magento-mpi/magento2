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
     * @var DesignInterface
     */
    private $design;

    /**
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    public function __construct(DesignInterface $design, \Magento\UrlInterface $baseUrl)
    {
        $this->design = $design;
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
        $defaults = $this->design->getDesignParams();
        $theme = $this->design->getDesignTheme();
        $relPath = self::getFullyQualifiedPath($filePath, $defaults['area'], $theme, $defaults['locale'], $module);
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecure));
        return $baseUrl . $relPath;
    }

    /**
     * Build a fully qualified path to view file using specified components
     *
     * @param string $filePath
     * @param string $areaCode
     * @param ThemeInterface $theme
     * @param string $localeCode
     * @param string $module
     * @return string
     */
    public static function getFullyQualifiedPath($filePath, $areaCode, ThemeInterface $theme, $localeCode, $module = '')
    {
        if ($theme->getThemePath()) {
            $designPath = $theme->getThemePath();
        } elseif ($theme->getId()) {
            $designPath = self::PUBLIC_THEME_DIR . $theme->getId();
        } else {
            $designPath = self::PUBLIC_VIEW_DIR;
        }
        return $areaCode . '/' . $designPath . '/' . $localeCode . ($module ? '/' . $module : '') . '/' . $filePath;
    }
}

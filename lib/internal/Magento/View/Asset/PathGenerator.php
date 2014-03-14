<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

use Magento\View\Design\ThemeInterface;

/**
 * Builder of relative paths basing on input parameters
 */
class PathGenerator
{
    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * @var string
     */
    protected $appMode;

    /**
     * @param string $appMode
     */
    public function __construct($appMode = \Magento\App\State::MODE_DEFAULT)
    {
        $this->appMode = $appMode;
    }

    /**
     * Get relative path basing on input parameters, taking into account theme path
     *
     * @param string $areaCode
     * @param ThemeInterface $theme
     * @param string $localeCode
     * @param string $module
     * @return string
     */
    public function getPathUsingTheme($areaCode, ThemeInterface $theme, $localeCode, $module = '')
    {
        $themePath = $this->getThemePath($theme);
        return $this->getPath($areaCode, $themePath, $localeCode, $module);
    }

    /**
     * Convert theme model into a theme path literal
     *
     * @param ThemeInterface $theme
     * @return string
     */
    public function getThemePath(ThemeInterface $theme)
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
        return $themePath;
    }

    /**
     * Get relative path basing on input parameters
     *
     * @param string $areaCode
     * @param string $themePath
     * @param string $localeCode
     * @param string $module
     * @return string
     */
    public function getPath($areaCode, $themePath, $localeCode, $module = '')
    {
        if ($this->appMode == \Magento\App\State::MODE_PRODUCTION) {
            $localeCode = ''; // a workaround while support for locale is not implemented in production mode
        }
        return $areaCode . '/' . $themePath . ($localeCode ? '/' . $localeCode : '') . ($module ? '/' . $module : '');
    }
}

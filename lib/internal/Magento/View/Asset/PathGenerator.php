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
        return $areaCode . '/' . $themePath . '/' . $localeCode . ($module ? '/' . $module : '');
    }
}

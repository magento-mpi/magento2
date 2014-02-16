<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Design\Theme;

/**
 * Interface ThemeProviderInterface
 */
interface ThemeProviderInterface
{
    /**
     * Get theme from DB by area and theme_path
     *
     * @param string $fullPath
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getThemeByFullPath($fullPath);

    /**
     * Filter theme customization
     *
     * @param string $area
     * @param int $type
     * @return array
     */
    public function getThemeCustomizations($area, $type = \Magento\View\Design\ThemeInterface::TYPE_VIRTUAL);

    /**
     * @param int $themeId
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getThemeById($themeId);
}

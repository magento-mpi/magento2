<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme;

/**
 * Theme list interface
 */
interface ListInterface
{
    /**
     * Get theme by area and theme_path
     *
     * @param string $fullPath
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getThemeByFullPath($fullPath);
}

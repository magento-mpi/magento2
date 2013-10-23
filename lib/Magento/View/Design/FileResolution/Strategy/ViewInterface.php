<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for 'view' file resolution strategy
 */
namespace Magento\View\Design\FileResolution\Strategy;

use Magento\View\Design\ThemeInterface;

/**
 * View Interface
 *
 * @package Magento\View
 */
interface ViewInterface
{
    /**
     * Get theme file name (e.g. a javascript file)
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, ThemeInterface $themeModel, $locale, $file, $module = null);
}

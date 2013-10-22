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
namespace Magento\Core\Model\Design\FileResolution\Strategy;

interface ViewInterface
{
    /**
     * Get theme file name (e.g. a javascript file)
     *
     * @param string $area
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, \Magento\View\Design\ThemeInterface $themeModel, $locale, $file, $module = null);
}

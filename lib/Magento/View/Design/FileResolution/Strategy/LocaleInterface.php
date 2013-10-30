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
 * Interface for 'locale' file resolution strategy
 */
namespace Magento\View\Design\FileResolution\Strategy;

use Magento\View\Design\ThemeInterface;

/**
 * Locale Interface
 *
 * @package Magento\View
 */
interface LocaleInterface
{
    /**
     * Get locale file name (e.g. file with translations)
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, ThemeInterface $themeModel, $locale, $file);
}

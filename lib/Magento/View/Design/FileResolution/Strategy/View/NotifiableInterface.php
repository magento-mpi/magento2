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
 * Interface for a view strategy to be notifiable, that file location has changed
 */
namespace Magento\View\Design\FileResolution\Strategy\View;

use Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy;
use Magento\View\Design\ThemeInterface;

/**
 * Notifiable Interface
 *
 * @package Magento\View
 */
interface NotifiableInterface
{
    /**
     * Notify the strategy, that file has changed its location, and next time should be resolved to this
     * new location.
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $locale
     * @param string|null $module
     * @param string $file
     * @param string $newFilePath
     * @return CachingProxy
     */
    public function setViewFilePathToMap($area, ThemeInterface $themeModel, $locale, $module, $file, $newFilePath);
}

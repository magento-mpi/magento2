<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Strategy\View;

use Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy;
use Magento\View\Design\ThemeInterface;

/**
 * Notifiable Interface
 *
 * Interface for a view strategy to be notifiable, that file location has changed
 */
interface NotifiableInterface
{
    /**
     * Set view file path to map
     *
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

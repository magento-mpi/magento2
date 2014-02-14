<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Strategy;

use Magento\View\Design\ThemeInterface;

/**
 * File Interface
 *
 * Interface for 'file' file resolution strategy
 */
interface FileInterface
{
    /**
     * Get a usual file path (e.g. template)
     *
     * @param string $area
     * @param ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, ThemeInterface $themeModel, $file, $module = null);
}

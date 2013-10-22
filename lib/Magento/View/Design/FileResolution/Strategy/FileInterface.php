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
 * Interface for 'file' file resolution strategy
 */
namespace Magento\View\Design\FileResolution\Strategy;

use Magento\View\Design\ThemeInterface;

/**
 * File Interface
 *
 * @package Magento\View
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

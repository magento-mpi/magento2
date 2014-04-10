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
 * Template Interface
 *
 * Interface for 'template' file resolution strategy
 */
interface TemplateInterface
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
    public function getTemplateFile($area, ThemeInterface $themeModel, $file, $module = null);
}

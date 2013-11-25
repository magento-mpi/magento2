<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File;

use Magento\View\Design\ThemeInterface;

/**
 * Interface of locating layout files in the file system
 */
interface SourceInterface
{
    /**
     * Retrieve instances of layout files
     *
     * @param ThemeInterface $theme Theme that defines the design context
     * @param string $filePath [optional]
     * @return \Magento\View\Layout\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*');
}

<?php
/**
 * Interface of locating layout files in the file system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File;

use Magento\View\Design\Theme;

interface Source
{
    /**
     * Retrieve instances of layout files
     *
     * @param Theme $theme Theme that defines the design context
     * @param string $filePath [optional]
     * @return \Magento\View\Layout\File[]
     */
    public function getFiles(Theme $theme, $filePath = '*');
}

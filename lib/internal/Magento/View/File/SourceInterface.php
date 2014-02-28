<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File;

use Magento\View\Design\ThemeInterface;

/**
 * Interface of locating view files in the file system
 */
interface SourceInterface
{
    /**
     * Retrieve instances of view files
     *
     * @param ThemeInterface $theme Theme that defines the design context
     * @param string $filePath [optional]
     * @return \Magento\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*');
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\File;

use Magento\Framework\View\Design\ThemeInterface;

/**
 * Interface of locating view files in the file system
 */
interface CollectorInterface
{
    /**
     * Retrieve instances of view files
     *
     * @param ThemeInterface $theme Theme that defines the design context
     * @param string $filePath
     * @return \Magento\Framework\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath);
}

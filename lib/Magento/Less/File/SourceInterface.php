<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File;

use Magento\View\Design\ThemeInterface;

/**
 * Interface of locating LESS files in the file system
 */
interface SourceInterface
{
    /**
     * Retrieve instances of LESS files
     *
     * @param string $filePath [optional]
     * @param ThemeInterface $theme Theme that defines the design context
     * @return \Magento\View\Layout\File[]
     */
    public function getFiles($filePath = '*', ThemeInterface $theme = null);
}

//TODO: Current class is similar to \Magento\View\Layout\File\SourceInterface


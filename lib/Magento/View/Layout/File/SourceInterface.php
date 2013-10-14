<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of locating layout files in the file system
 */
namespace Magento\View\Layout\File;

use Magento\Core\Model\ThemeInterface;

interface SourceInterface
{
    /**
     * Retrieve instances of layout files
     *
     * @param ThemeInterface $theme Theme that defines the design context
     * @param $filePath [optional]
     * @return \Magento\View\Layout\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath = '*');
}

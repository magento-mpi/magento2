<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\FileList;

/**
 * Layout file list collate interface
 */
interface CollateInterface
{
    /**
     * Collate layout files
     *
     * @param \Magento\View\Layout\File[] $files
     * @param \Magento\View\Layout\File[] $filesOrigin
     * @return \Magento\View\Layout\File[]
     */
    public function collate($files, $filesOrigin);
}

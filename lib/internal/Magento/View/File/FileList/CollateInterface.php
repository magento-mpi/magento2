<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File\FileList;

/**
 * View file list collate interface
 */
interface CollateInterface
{
    /**
     * Collate view files
     *
     * @param \Magento\View\File[] $files
     * @param \Magento\View\File[] $filesOrigin
     * @return \Magento\View\File[]
     */
    public function collate($files, $filesOrigin);
}

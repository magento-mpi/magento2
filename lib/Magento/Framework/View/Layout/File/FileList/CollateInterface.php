<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\File\FileList;

/**
 * Layout file list collate interface
 */
interface CollateInterface
{
    /**
     * Collate layout files
     *
     * @param \Magento\Framework\View\Layout\File[] $files
     * @param \Magento\Framework\View\Layout\File[] $filesOrigin
     * @return \Magento\Framework\View\Layout\File[]
     */
    public function collate($files, $filesOrigin);
}

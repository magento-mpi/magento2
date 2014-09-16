<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\Config\Collector;

/**
 * Interface of locating UI library configuration files in the file system
 */
interface CollectorInterface
{
    /**
     * Retrieve instances of UI library configuration files
     *
     * @param string $filePath
     * @return \Magento\Framework\View\File[]
     */
    public function getFiles($filePath);
}

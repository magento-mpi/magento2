<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config;

class FileIteratorFactory
{
    /**
     * Create file iterator
     *
     * @param string $basePath
     * @param array $paths
     * @return FileIterator
     */
    public function create($basePath, $paths)
    {
        return new FileIterator($basePath, $paths);
    }
}

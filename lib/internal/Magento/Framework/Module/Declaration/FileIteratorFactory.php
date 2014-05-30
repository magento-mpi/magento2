<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Declaration;

use Magento\Framework\Filesystem;

class FileIteratorFactory
{
    /**
     * Creates a FileIterator.
     *
     * @param Filesystem $filesystem
     * @param string[] paths
     * @return FileIterator
     */
    public function create($filesystem, $paths)
    {
        return new FileIterator($filesystem, $paths);
    }
}

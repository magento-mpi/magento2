<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\FileResolver;

class FileIteratorFactory
{
    public function create($filesystem, $paths)
    {
        return new \Magento\App\Config\FileResolver\FileIterator($filesystem, $paths);
    }
}

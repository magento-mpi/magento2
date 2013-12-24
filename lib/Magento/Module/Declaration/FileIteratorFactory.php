<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration;

class FileIteratorFactory
{
    public function create($filesystem, $paths)
    {
        return new \Magento\Module\Declaration\FileIterator($filesystem, $paths);
    }
}

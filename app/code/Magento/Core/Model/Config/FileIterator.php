<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class FileIterator extends \Magento\Config\FileIterator
{
    public function __construct(
        \Magento\Filesystem $filesystem,
        array $paths
    ){
        parent::__construct($filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG), $paths);
    }
}

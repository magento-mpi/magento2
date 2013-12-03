<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration;

class FileIterator extends \Magento\Config\FileIterator
{

    public function __construct(
        \Magento\Filesystem $filesystem,
        array $paths
    ) {
        parent::__construct($filesystem->getDirectoryRead(\Magento\Filesystem::APP), $paths);
    }

}

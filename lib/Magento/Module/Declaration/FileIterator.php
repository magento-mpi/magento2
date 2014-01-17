<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration;

use Magento\Filesystem;

class FileIterator extends \Magento\Config\FileIterator
{

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem
     * @param string[] $paths
     */
    public function __construct(
        Filesystem $filesystem,
        array $paths
    ) {
        parent::__construct($filesystem->getDirectoryRead(Filesystem::APP), $paths);
    }

}

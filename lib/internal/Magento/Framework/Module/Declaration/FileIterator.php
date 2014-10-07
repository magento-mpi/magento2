<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Declaration;

use Magento\Framework\App\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class FileIterator extends \Magento\Framework\Config\FileIterator
{
    /**
     * Constructor.
     *
     * @param Filesystem $filesystem
     * @param string[] $paths
     */
    public function __construct(Filesystem $filesystem, array $paths)
    {
        parent::__construct($filesystem->getDirectoryRead(DirectoryList::APP_DIR), $paths);
    }
}

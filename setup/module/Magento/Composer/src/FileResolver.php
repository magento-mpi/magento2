<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Composer;

use Zend\Stdlib\Glob;
use Magento\Config\FileResolverInterface;
use Magento\Config\FileIterator;
use Magento\Framework\App\Filesystem\DirectoryList;

class FileResolver implements FileResolverInterface
{
    /**
     * Magento application's DirectoryList
     *
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * Constructor
     *
     * @param DirectoryList $directoryList
     */
    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Collect files and wrap them into an Iterator object
     *
     * @param string $filename
     * @return FileIterator
     */
    public function get($filename)
    {
        $pattern = $this->directoryList->getPath(DirectoryList::MODULES) . '/*/*/' . $filename;
        return new FileIterator(Glob::glob($pattern));
    }
}

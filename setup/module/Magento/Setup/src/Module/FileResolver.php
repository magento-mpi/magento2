<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module;

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
        $result = [];

        // Collect files by /app/code/*/*/etc/{filename} pattern
        $pattern = $this->directoryList->getPath(DirectoryList::MODULES) . '/*/*/etc/' . $filename;
        foreach (Glob::glob($pattern) as $file) {
            $result[] = $file;
        }

        // Collect files by /app/etc/*/{filename} pattern
        $pattern = $this->directoryList->getPath(DirectoryList::CONFIG) . '/*/' . $filename;
        foreach (Glob::glob($pattern) as $file) {
            $result[] = $file;
        }

        return new FileIterator($result);
    }
}

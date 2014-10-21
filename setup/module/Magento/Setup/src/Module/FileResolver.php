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
use Magento\Config\FileIteratorFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class FileResolver implements FileResolverInterface
{
    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @param FileIteratorFactory $iteratorFactory
     * @param DirectoryList $directoryList
     */
    public function __construct(
        FileIteratorFactory $iteratorFactory,
        DirectoryList $directoryList
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->directoryList = $directoryList;
    }

    /**
     * @param string $filename
     * @return array
     */
    public function get($filename)
    {
        $paths = [];
        $root = $this->directoryList->getRoot();

        // Collect files by /app/code/*/*/etc/{filename} pattern
        $pattern = $this->directoryList->getPath(DirectoryList::MODULES) . '/*/*/etc/' . $filename;
        foreach (Glob::glob($pattern) as $file) {
            $paths[] = substr($file, strlen($root));
        }

        // Collect files by /app/etc/*/{filename} pattern
        $pattern = $this->directoryList->getPath(DirectoryList::CONFIG) . '/*/' . $filename;
        foreach (Glob::glob($pattern) as $file) {
            $paths[] = substr($file, strlen($root));
        }

        return $this->iteratorFactory->create($root, $paths);
    }
}

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
use Magento\Config\FileIteratorFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class FileResolver implements FileResolverInterface
{
    /**
     * File iterator factory
     *
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * Magento application's DirectoryList
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * Constructor
     *
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
     * Collect files and wrap them into an Iterator object
     *
     * @param string $filename
     * @return array
     */
    public function get($filename)
    {
        $result = [];
        $root = $this->directoryList->getRoot();
        $pattern = $this->directoryList->getPath(DirectoryList::MODULES) . '/*/*/' . $filename;
        foreach (Glob::glob($pattern) as $file) {
            $result[] = substr($file, strlen($root));
        }
        return $this->iteratorFactory->create($root, $result);
    }
}

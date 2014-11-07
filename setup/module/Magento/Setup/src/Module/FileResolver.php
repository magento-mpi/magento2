<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Filesystem\Directory\ReadInterface;

class FileResolver implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * Modules directory with read access
     *
     * @var ReadInterface
     */
    protected $modulesDirectory;

    /**
     * Config directory with read access
     *
     * @var ReadInterface
     */
    protected $configDirectory;

    /**
     * File Iterator
     *
     * Root directory with read access
     *
     * @var ReadInterface
     */
    protected $rootDirectory;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        FileIteratorFactory $iteratorFactory
    ) {
        $this->modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
        $this->configDirectory = $filesystem->getDirectoryRead(DirectoryList::CONFIG);
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * Collect files and wrap them into an Iterator object
     *
     * @param string $filename
     * @param string $scope
     * @return \Magento\Framework\Config\FileIterator
     */
    public function get($filename, $scope)
    {
        $moduleDir = $this->modulesDirectory->getAbsolutePath();
        $configDir = $this->configDirectory->getAbsolutePath();

        $output = [];
        $output = array_merge($output, $this->aggregateFiles(glob($moduleDir . '/*/*/etc/' . $filename)));
        $output = array_merge($output, $this->aggregateFiles(glob($configDir . '/*/' . $filename)));
        $iterator = $this->iteratorFactory->create(
            $this->rootDirectory,
            $output
        );
        return $iterator;
    }

    /**
     * Collect files and wrap them into an Iterator object
     *
     * @param array $files
     * @return array
     */
    protected function aggregateFiles($files)
    {
        $output = [];
        if (!empty($files)) {
            foreach ($files as $file) {
                $output[] = $this->rootDirectory->getRelativePath($file);
            }
        }
        return $output;
    }
}

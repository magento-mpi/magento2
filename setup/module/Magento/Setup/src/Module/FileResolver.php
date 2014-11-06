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
     * @param \Magento\Framework\Config\FileIteratorFactory $iteratorFactory
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
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        $moduleDir = $this->modulesDirectory->getAbsolutePath();
        $configDir = $this->configDirectory->getAbsolutePath();

        $output = [];
        $files = glob($moduleDir . '/*/*/etc/' . $filename);
        if (!empty($files)) {
            foreach ($files as $file) {
                $output[] = $this->rootDirectory->getRelativePath($file);
            }
        }
        $files = glob($configDir . '/*/' . $filename);
        if (!empty($files)) {
            foreach ($files as $file) {
                $output[] = $this->rootDirectory->getRelativePath($file);
            }
        }

        $iterator = $this->iteratorFactory->create(
            $this->rootDirectory,
            $output
        );
        return $iterator;
    }
}

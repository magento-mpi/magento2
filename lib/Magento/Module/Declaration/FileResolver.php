<?php
/**
 * Module declaration file resolver. Reads list of module declaration files from module /etc directories.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Module\Declaration;

use Magento\App\Filesystem;
use Magento\Filesystem\Directory\ReadInterface;

class FileResolver implements \Magento\Config\FileResolverInterface
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
     * File iterator factory
     *
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->iteratorFactory  = $iteratorFactory;
        $this->modulesDirectory = $filesystem->getDirectoryRead(Filesystem::MODULES_DIR);
        $this->configDirectory  = $filesystem->getDirectoryRead(Filesystem::CONFIG_DIR);
        $this->rootDirectory    = $filesystem->getDirectoryRead(Filesystem::ROOT_DIR);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $moduleDir = $this->modulesDirectory->getAbsolutePath();
        $configDir = $this->configDirectory->getAbsolutePath();

        $mageScopePath = $moduleDir . '/Magento';
        $output = array(
            'base' => array(),
            'mage' => array(),
            'custom' => array(),
        );
        $files = glob($moduleDir . '*/*/etc/module.xml');
        foreach ($files as $file) {
            $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
            $output[$scope][] = $this->rootDirectory->getRelativePath($file);
        }
        $files = glob($configDir . '*/module.xml');
        foreach ($files as $file) {
            $output['base'][] = $this->rootDirectory->getRelativePath($file);
        }
        return $this->iteratorFactory->create(
            $this->rootDirectory,
            array_merge($output['mage'], $output['custom'], $output['base'])
        );
    }
}

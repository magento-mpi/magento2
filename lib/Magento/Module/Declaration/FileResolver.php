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

use Magento\Filesystem\Directory\ReadInterface;
use Magento\Filesystem;
use Magento\Config\FileIteratorFactory;

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
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param Filesystem $filesystem
     * @param FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        Filesystem $filesystem,
        FileIteratorFactory $iteratorFactory
    ) {
        $this->iteratorFactory      = $iteratorFactory;
        $this->modulesDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::MODULES);
        $this->configDirectory  = $filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
        $this->rootDirectory     = $filesystem->getDirectoryRead(\Magento\Filesystem::ROOT);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $moduleDir = $this->modulesDirectory->getAbsolutePath();
        $configDir =  $this->configDirectory->getAbsolutePath();

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

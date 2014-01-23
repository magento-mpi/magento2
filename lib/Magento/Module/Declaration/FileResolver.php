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

class FileResolver implements \Magento\Config\FileResolverInterface
{
    /**
     * Modules directory with read access
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $modulesDirectory;

    /**
     * Config directory with read access
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $configDirectory;

    /**
     * Root directory with read access
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * File iterator factory
     *
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->iteratorFactory      = $iteratorFactory;
        $this->modulesDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::MODULES_DIR);
        $this->configDirectory  = $filesystem->getDirectoryRead(\Magento\App\Filesystem::CONFIG_DIR);
        $this->rootDirectory     = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
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

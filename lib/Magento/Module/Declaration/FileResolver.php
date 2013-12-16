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
    protected $directoryReadModule;

    /**
     * Config directory with read access
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryReadConfig;

    /**
     * Root directory with read access
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryReadRoot;

    /**
     * File iterator factory
     *
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->iteratorFactory      = $iteratorFactory;
        $this->directoryReadModules = $filesystem->getDirectoryRead(\Magento\Filesystem::MODULES);
        $this->directoryReadConfig  = $filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
        $this->directoryReadRoot     = $filesystem->getDirectoryRead(\Magento\Filesystem::ROOT);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $appCodeDir =  $this->directoryReadRoot->getRelativePath(
            $this->directoryReadModules->getAbsolutePath()
        );
        $configDir =  $this->directoryReadRoot->getRelativePath(
            $this->directoryReadConfig->getAbsolutePath()
        );
        $moduleFileList = $this->directoryReadRoot->search('#.*?/module.xml$#', $appCodeDir);

        $mageScopePath = $appCodeDir . '/Magento/';
        $output = array(
            'base' => array(),
            'mage' => array(),
            'custom' => array(),
        );
        foreach ($moduleFileList as $file) {
            $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
            $output[$scope][] = $file;
        }
        $output['base'] = $this->directoryReadRoot->search('#/module.xml$#', $configDir);

        return $this->iteratorFactory->create(
            $this->directoryReadRoot,
            array_merge($output['mage'], $output['custom'], $output['base'])
        );
    }
}

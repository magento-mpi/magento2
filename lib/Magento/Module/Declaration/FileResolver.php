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
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryReadModule;
    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryReadConfig;
    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryReadApp;
    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Module\Declaration\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Module\Declaration\FileIteratorFactory $iteratorFactory
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->filesystem = $filesystem;
        $this->directoryReadModules = $filesystem->getDirectoryRead(\Magento\Filesystem::MODULES);
        $this->directoryReadConfig = $filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
        $this->directoryReadApp = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $appCodeDir =  $this->directoryReadApp->getRelativePath(
            $this->directoryReadModules->getAbsolutePath()
        );
        $configDir =  $this->directoryReadApp->getRelativePath(
            $this->directoryReadConfig->getAbsolutePath()
        );
        $moduleFileList = $this->directoryReadApp->search('#.*?/module.xml$#', $appCodeDir);

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
        $output['base'] = $this->directoryReadApp->search('#/module.xml$#', $configDir);

        return $this->iteratorFactory->create($this->filesystem,
            array_merge($output['mage'], $output['custom'], $output['base'])
        );
    }
}

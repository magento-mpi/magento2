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
    protected $_modulesDirectory;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $_configDirectory;

    /**
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        $this->_modulesDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::MODULES);
        $this->_configDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $output = array(
            'base' => array(),
            'mage' => array(),
            'custom' => array(),
        );

        $files = $this->_modulesDirectory->search('#.*?\/.*?\/etc\/module\.xml#');
        $mageScopePath = 'Magento/';
        foreach ($files as $file) {
            $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
            $output[$scope][] = $this->_modulesDirectory->getAbsolutePath($file);
        }

        $baseFiles = $this->_configDirectory->search('#' . preg_quote('.*?\/module\.xml') . '#');
        foreach ($baseFiles as $file) {
            $output['base'][] = $this->_configDirectory->getAbsolutePath($file);
        }

        // Put global enablers at the end of the file list
        return array_merge($output['mage'], $output['custom'], $output['base']);
    }

}

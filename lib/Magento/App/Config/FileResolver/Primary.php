<?php
/**
 * Application primary config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\FileResolver;

/***
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Primary implements \Magento\Config\FileResolverInterface
{
    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $_configDirectory;

    /**
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\Filesystem $filesystem)
    {
        $this->_configDirectory = $filesystem->getDirectoryRead(\Magento\Filesystem::CONFIG);
    }

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $fileList = $this->_configDirectory->search('*/' . $filename);

        if ($this->_configDirectory->isFile($filename)) {
            array_unshift($fileList, $this->_configDirectory->getAbsolutePath($filename));
        }
        return $fileList;
    }
}

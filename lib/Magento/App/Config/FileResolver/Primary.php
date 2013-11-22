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
     * @var string
     */
    protected $_configDirectoryPath;

    /**
     * @param string $configDirectoryPath
     */
    public function __construct($configDirectoryPath)
    {
        $this->_configDirectoryPath = $configDirectoryPath;
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
        $fileList = glob($this->_configDirectoryPath . '/*/' . $filename);

        if (is_file($this->_configDirectoryPath . '/' . $filename)) {
            array_unshift($fileList, $this->_configDirectoryPath . '/' . $filename);
        }

        return $fileList;
    }
}

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
     * @var \Magento\App\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\App\Dir $dirs
     */
    public function __construct(\Magento\App\Dir $dirs)
    {
        $this->_applicationDirs = $dirs;
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
        $configDir = $this->_applicationDirs->getDir(\Magento\App\Dir::CONFIG);
        $fileList = glob($configDir . '/*/' . $filename);

        if (file_exists($configDir . '/' . $filename)) {
            array_unshift($fileList, $configDir . '/' . $filename);
        }
        return $fileList;
    }
}

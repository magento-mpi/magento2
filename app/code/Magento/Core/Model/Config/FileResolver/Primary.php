<?php
/**
 * Application primary config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\FileResolver;

/***
 *@SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Primary implements \Magento\Config\FileResolverInterface
{
    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\Core\Model\Dir $dirs
     */
    public function __construct(\Magento\Core\Model\Dir $dirs)
    {
        $this->_applicationDirs = $dirs;
    }

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    public function get($filename, $scope)
    {
        $configDir = $this->_applicationDirs->getDir(\Magento\Core\Model\Dir::CONFIG);
        $fileList = glob($configDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $filename);

        if (file_exists($configDir . DIRECTORY_SEPARATOR . $filename)) {
            array_unshift($fileList, $configDir . DIRECTORY_SEPARATOR . $filename);
        }
        return $fileList;
    }
}

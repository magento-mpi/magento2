<?php
/**
 * Application primary config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_FileResolver_Primary implements \Magento\Config\FileResolverInterface
{
    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_applicationDirs;

    /**
     * @param Magento_Core_Model_Dir $dirs
     */
    public function __construct(Magento_Core_Model_Dir $dirs)
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
        $configDir = $this->_applicationDirs->getDir(Magento_Core_Model_Dir::CONFIG);
        $fileList = glob($configDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $filename);

        if (file_exists($configDir . DIRECTORY_SEPARATOR . $filename)) {
            array_unshift($fileList, $configDir . DIRECTORY_SEPARATOR . $filename);
        }
        return $fileList;
    }
}

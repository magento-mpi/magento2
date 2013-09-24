<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Widget_Model_Config_FileResolver implements Magento_Config_FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_applicationDirs;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Core_Model_Dir $applicationDirs
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Core_Model_Dir $applicationDirs
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $fileList = array();
        switch ($scope) {
            case 'global':
                $fileList = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            case 'design':
                $fileList = glob($this->_applicationDirs->getDir(Magento_Core_Model_Dir::THEMES)
                . "/*/*/etc/$filename", GLOB_NOSORT | GLOB_BRACE);
                break;
            default:
                break;
        }
        return $fileList;
    }
}

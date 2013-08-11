<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_FileResolver implements Magento_Config_FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_applicationDirs;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Mage_Core_Model_Dir $applicationDirs
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Mage_Core_Model_Dir $applicationDirs
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                $appConfigDir = $this->_applicationDirs->getDir(Mage_Core_Model_Dir::CONFIG);
                // Create pattern similar to app/etc/{*config.xml,*/*config.xml}
                $filePattern = $appConfigDir . DIRECTORY_SEPARATOR
                    . '{*' . $filename . ',*' . DIRECTORY_SEPARATOR . '*' . $filename . '}';
                $fileList = glob($filePattern, GLOB_BRACE);
                break;
            case 'global':
                $fileList = $this->_moduleReader->getModuleConfigurationFiles($filename);
                break;
            default:
                $fileList = $this->_moduleReader->getModuleConfigurationFiles($scope . DIRECTORY_SEPARATOR . $filename);
                break;
        }
        return $fileList;
    }
}

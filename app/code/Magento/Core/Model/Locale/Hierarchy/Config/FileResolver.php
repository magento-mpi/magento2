<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Locale_Hierarchy_Config_FileResolver implements Magento_Config_FileResolverInterface
{

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_applicationDirs;

    /**
     * @param Mage_Core_Model_Dir $applicationDirs
     */
    public function __construct(Mage_Core_Model_Dir $applicationDirs)
    {
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $appLocaleDir = $this->_applicationDirs->getDir(Mage_Core_Model_Dir::LOCALE);
        // Create pattern similar to app/locale/*/config.xml
        $filePattern = $appLocaleDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $filename;
        $fileList = glob($filePattern, GLOB_BRACE);
        return $fileList;
    }
}

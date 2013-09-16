<?php
/**
 * Tests that existing logging.xml files are valid to schema individually and merged.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_LoggingConfigFilesTest extends Magento_TestFramework_TestCase_ConfigFilesAbstract
{
    /**
     * Returns the reader class name that will be instantiated via ObjectManager
     *
     * @return string reader class name
     */
    protected function _getReaderClassName()
    {
        return 'Magento_Logging_Model_Config_Reader';
    }

    /**
     * Returns a string that represents the path to the config file, starting in the app directory.
     *
     * Format is glob, so * is allowed.
     *
     * @return string
     */
    protected function _getConfigFilePathGlob()
    {
        return '/*/*/*/etc/logging.xml';
    }

    /**
     * Returns a path to the per file XSD file, relative to the modules directory.
     *
     * @return string
     */
    protected function _getXsdPath()
    {
        return '/Magento/Logging/etc/logging_file.xsd';
    }
}

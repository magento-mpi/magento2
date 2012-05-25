<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_Modular_MenuConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Configuration menu file list
     * @var array
     */
    protected $_fileList = array();

    /**
     * Get Configuration File List
     * @return array
     */
    protected function _getConfigurationFileList()
    {
        if (empty($this->_fileList)) {
            foreach (glob(Mage::getBaseDir('app') . '/*/*/*/*/etc/adminhtml/menu.xml') as $file) {
                $this->_fileList[$file] = $file;
            }
        }
        return $this->_fileList;
    }

    /**
     * Get merged menu configuration
     * @return Magento_Config_Dom
     */
    protected function _mergeConfigurationFiles()
    {
        $xml = '<menu>';
        foreach ($this->_getConfigurationFileList() as $file) {
            $element = simplexml_load_file($file);
            foreach ($element->children() as $node) {
                $xml .= $node->asXml();
            }
        }
        $xml .= '</menu>';
        return new Magento_Config_Dom($xml);
    }

    /**
     * Perform test whether a configuration file is valid
     *
     * @param string $file
     * @param Magento_Config_Dom $domConfig
     * @throws PHPUnit_Framework_AssertionFailedError if file is invalid
     */
    protected function _validateConfigFile($file, $domConfig = null)
    {
        $schemaFile = Mage::getBaseDir('lib') . '/Magento/Config/menu.xsd';

        if (false == ($domConfig instanceof Magento_Config_Dom)) {
            $domConfig = new Magento_Config_Dom(file_get_contents($file));
        }
        $result = $domConfig->validate($schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * Test merged menu configuration
     */
    public function testMergedConfig()
    {
        $dom = $this->_mergeConfigurationFiles();
        $this->_validateConfigFile('merged', $dom);
    }

    /**
     * Test each menu configuration file
     */
    public function testMenuConfigFile()
    {
        $list = $this->_getConfigurationFileList();
        if (empty($list)) {
            $this->markTestSkipped('Menu configuration files not found');
        } else {
            foreach ($list as $file) {
                $this->_validateConfigFile($file);
            }
        }
    }
}

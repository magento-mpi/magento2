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
     * Mage_Backend_Model_Config_Menu
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Menu_Config_Menu',
            array(),
            '',
            false
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_fileList = null;
    }

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
     * Perform test whether a configuration file is valid
     *
     * @param string $file
     * @throws PHPUnit_Framework_AssertionFailedError if file is invalid
     */
    protected function _validateConfigFile($file)
    {

        $schemaFile = $this->_model->getSchemaFile();
        $domConfig = new Magento_Config_Dom(file_get_contents($file));
        $result = $domConfig->validate($schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * Test each menu configuration file
     * @param string $file
     * @dataProvider menuConfigFileDataProvider
     */
    public function testMenuConfigFile($file)
    {
        $this->_validateConfigFile($file);
    }

    /**
     * @return array
     */
    public function menuConfigFileDataProvider()
    {
        $output = array();
        $list = $this->_getConfigurationFileList();
        foreach ($list as $file) {
            $output[$file] = array($file);
        }
        return $output;
    }

    /**
     * Test merged menu configuration
     */
    public function testMergedConfig()
    {
        $model = new Mage_Backend_Model_Menu_Config_Menu($this->_getConfigurationFileList());
        try {
            $this->assertInstanceOf('Mage_Backend_Model_Menu_Config_Menu', $model->validate());
        } catch (Magento_Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}

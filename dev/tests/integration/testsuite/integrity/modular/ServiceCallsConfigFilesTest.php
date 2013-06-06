<?php
/**
 * Tests that existing service_calls.xml files are valid to schema.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Modular_ServiceCallsConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to schema file
     *
     * @var string
     */
    protected $_schemaFile;

    /** @var  Mage_Core_Model_DataService_Config_Reader */
    protected $_reader;

    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * Create a config reader and put it in the object manager and schema file information
     */
    public function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_reader = $this->_objectManager->get('Mage_Core_Model_DataService_Config_Reader');
        $this->_schemaFile = $this->_reader->getSchemaFile();
    }

    /**
     * Delete the config reader from the object manager
     */
    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Mage_Core_Model_DataService_Config_Reader');
    }

    /**
     * Test individual service_calls configuration files
     *
     * @param string $file
     * @dataProvider serviceCallsConfigFileDataProvider
     */
    public function testServiceCallsConfigFile($file)
    {
        $domConfig = new Magento_Config_Dom(file_get_contents($file));
        $result = $domConfig->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * Find all service_calls.xml files
     *
     * @return array
     */
    public function serviceCallsConfigFileDataProvider()
    {
        $fileList = glob(Mage::getBaseDir('app') . '/*/*/*/etc/service_calls.xml');
        $dataProviderResult = array();
        foreach ($fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }

    /**
     * Test merged service_calls configuration for conformance to schema.
     */
    public function testMergedConfiguration()
    {
        $dom = $this->_reader->getServiceCallConfig();
        $domConfig = new Magento_Config_Dom($dom->getXmlString());
        $result = $domConfig->validate($this->_schemaFile, $errors);
        $message = "Invalid merged service_calls config\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }
        $this->assertTrue($result, $message);
    }
}

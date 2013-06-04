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

class Integrity_Modular_ServiceCallsConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $_fileList = array();

    /**
     * Path to schema file
     *
     * @var string
     */
    protected $_schemaFile;


    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $reader = $this->_objectManager->get('Mage_Core_Model_Dataservice_Config_Reader');
        $this->_schemaFile = $reader->getSchemaFile();
        $this->_prepareFileList();
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Mage_Core_Model_Dataservice_Config_Reader');
    }

    /**
     * Prepare file list of ACL resources
     */
    protected function _prepareFileList()
    {
        if (empty($this->_fileList)) {
            $this->_fileList = glob(Mage::getBaseDir('app') . '/*/*/*/etc/service_calls.xml');
        }
    }

    /**
     * Test each acl configuration file
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
     * @return array
     */
    public function serviceCallsConfigFileDataProvider()
    {
        $this->_prepareFileList();
        $dataProviderResult = array();
        foreach ($this->_fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }

//    /**
//     * Test merged ACL configuration
//     */
//    public function testMergedConfiguration()
//    {
//        /** @var $dom Magento_Acl_Config_Reader **/
//        $dom = Mage::getModel('Magento_Acl_Config_Reader', array('configFiles' => $this->_fileList))
//            ->getAclResources();
//
//        $domConfig = new Magento_Acl_Config_Reader_Dom($dom->saveXML());
//        $errors = array();
//        $result = $domConfig->validate($this->_schemaFile, $errors);
//        $message = "Invalid merged ACL config\n";
//        foreach ($errors as $error) {
//            $message .= "{$error->message} Line: {$error->line}\n";
//        }
//        $this->assertTrue($result, $message);
//    }
}

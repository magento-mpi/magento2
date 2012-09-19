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

class Integrity_Modular_AclConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $_fileList = array();

    /**
     * Path to scheme file
     *
     * @var string
     */
    protected $_schemeFile;

    public function setUp()
    {
        $readerMock = $this->getMock('Magento_Acl_Config_Reader', array('getShemaFile'), array(), '', false);
        $this->_schemeFile = $readerMock->getSchemaFile();
        $this->_prepareFileList();
    }

    /**
     * Prepare file list of ACL resources
     *
     * @return void
     */
    protected function _prepareFileList()
    {
        if (empty($this->_fileList)) {
            $this->_fileList = glob(Mage::getBaseDir('app') . '/*/*/*/*/etc/adminhtml/acl.xml');
        }
    }

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider aclConfigFileDataProvider
     */
    public function testAclConfigFile($file)
    {
        $domConfig = new Magento_Config_Dom(file_get_contents($file));
        $result = $domConfig->validate($this->_schemeFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function aclConfigFileDataProvider()
    {
        $this->_prepareFileList();
        $dataProviderResult = array();
        foreach ($this->_fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }

    /**
     * Test merged ACL configuration
     */
    public function testMergedConfiguration()
    {
        /** @var $dom Magento_Acl_Config_Reader **/
        $dom = Mage::getModel('Magento_Acl_Config_Reader', $this->_fileList)->getAclResources();

        $domConfig = new Magento_Acl_Config_Reader_Dom($dom->saveXML());
        $errors = array();
        $result = $domConfig->validate($this->_schemeFile, $errors);
        $message = "Invalid merged ACL config\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }
}

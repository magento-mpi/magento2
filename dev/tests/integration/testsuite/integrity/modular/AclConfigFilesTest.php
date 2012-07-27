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
        $this->_schemeFile = Mage::getModuleDir(null, 'Mage_Backend') . DIRECTORY_SEPARATOR
            . 'Model' . DIRECTORY_SEPARATOR . 'Acl' . DIRECTORY_SEPARATOR . 'acl.xsd';
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
        if (empty($this->_fileList)) {
            foreach (glob(Mage::getBaseDir('app') . '/*/*/*/*/etc/adminhtml/acl.xml') as $file) {
                $this->_fileList[$file] = array($file);
            }
        }
        return $this->_fileList;
    }

    /**
     * Test merged ACL configuration
     */
    public function testMergedConfiguration()
    {
        /** @var $dom DOMDocument **/
        $dom = Mage::getModel('Mage_Backend_Model_Acl_Config_Reader')->getMergedAclResources();

        $domConfig = new Magento_Config_Dom($dom->saveXML());
        $errors = array();
        $result = $domConfig->validate($this->_schemeFile, $errors);
        $message = "Invalid merged ACL config\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }
}

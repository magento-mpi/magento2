<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_MenuHierarchyConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $objectManager = Mage::getObjectManager();
        $this->_schemaFile = $objectManager->get('Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator')
            ->getSchema();
    }

    /**
     * @param string $file
     * @dataProvider menuHierarchyConfigFilesDataProvider
     */
    public function testMenuHierarchyConfigFiles($file)
    {
        $errors = array();
        $dom = new Magento_Config_Dom(file_get_contents($file));
        $result = $dom->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function menuHierarchyConfigFilesDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('{*/menuHierarchy.xml,menuHierarchy.xml}');

    }
}

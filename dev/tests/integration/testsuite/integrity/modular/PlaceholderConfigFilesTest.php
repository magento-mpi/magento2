<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Modular_PlaceholderConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $objectManager = Mage::getObjectManager();
        $this->_schemaFile = $objectManager->get('Magento_FullPageCache_Model_Config_SchemaLocator')->getSchema();
    }

    /**
     * @param string $file
     * @dataProvider placeholderConfigFilesDataProvider
     */
    public function testPlaceholderConfigFiles($file)
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
    public function placeholderConfigFilesDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('{*/placeholders.xml,placeholders.xml}');
    }
}

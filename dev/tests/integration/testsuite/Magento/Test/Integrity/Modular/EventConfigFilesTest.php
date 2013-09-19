<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_EventConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_schemaFile = $objectManager->get('Magento\Core\Model\Event\Config\SchemaLocator')->getSchema();
    }

    /**
     * @param string $file
     * @dataProvider eventConfigFilesDataProvider
     */
    public function testEventConfigFiles($file)
    {
        $errors = array();
        $dom = new \Magento\Config\Dom(file_get_contents($file));
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
    public function eventConfigFilesDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getConfigFiles('{*/events.xml,events.xml}');

    }
}

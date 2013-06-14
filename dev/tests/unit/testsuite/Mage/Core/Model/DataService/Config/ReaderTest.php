<?php
/**
 * Mage_Core_Model_DataService_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_DataService_Config_Reader */
    private $_configReader;

    /**
     * Prepare object manager with mocks of objects required by config reader.
     */
    public function setUp()
    {
        $path = array(__DIR__, '..', '_files', 'service_calls.xml');
        $path = realpath(implode(DIRECTORY_SEPARATOR, $path));
        $this->_configReader = new Mage_Core_Model_DataService_Config_Reader(array($path));
    }

    /**
     * Verify correct schema file is returned.
     */
    public function testGetSchemaFile()
    {
        $expectedPath = realpath(str_replace('/',
            DIRECTORY_SEPARATOR, __DIR__ . '/../../../../../../../../../app/code/Mage/Core/etc/service_calls.xsd'));
        $result = $this->_configReader->getSchemaFile();
        $this->assertNotNull($result);
        $this->assertEquals($expectedPath, $result, 'returned schema file is wrong');
    }
}

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

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_modulesReaderMock;

    /**
     * Prepare object manager with mocks of objects required by config reader.
     */
    public function setUp()
    {
        $path = array(__DIR__, '..', '_files', 'service_calls.xml');
        $path = realpath(implode(DIRECTORY_SEPARATOR, $path));
        $this->_modulesReaderMock = $this->getMockBuilder('Mage_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configReader = new Mage_Core_Model_DataService_Config_Reader(
            $this->_modulesReaderMock,
            array($path)
        );
    }

    /**
     * Verify correct schema file is returned.
     */
    public function testGetSchemaFile()
    {
        $etcDir = str_replace('/', DIRECTORY_SEPARATOR, 'app/code/Mage/Core/etc');
        $expectedPath = $etcDir . DIRECTORY_SEPARATOR . 'service_calls.xsd';
        $this->_modulesReaderMock->expects($this->any())->method('getModuleDir')
            ->with('etc', 'Mage_Core')
            ->will($this->returnValue($etcDir));
        $result = $this->_configReader->getSchemaFile();
        $this->assertNotNull($result);
        $this->assertEquals($expectedPath, $result, 'returned schema file is wrong');
    }
}

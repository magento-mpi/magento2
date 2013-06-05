<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    const NAMEPART = 'NAMEPART';

    /** @var Mage_Core_Model_Dataservice_Config_Reader */
    private $_configReader;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_modulesReaderMock;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_cacheTypes;

    public function setup()
    {
        $this->_modulesReaderMock = $this->getMockBuilder('Mage_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_cacheTypes = $this->getMockBuilder('Mage_Core_Model_Cache_Type_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configReader =
            new Mage_Core_Model_Dataservice_Config_Reader($this->_modulesReaderMock, $this->_cacheTypes);
    }

    public function testGetServiceCallConfigCaching()
    {
        $this->_cacheTypes->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        $result = $this->_configReader->getServiceCallConfig();
        $this->assertNotNull($result);

        $secondResult = $this->_configReader->getServiceCallConfig();
        $this->assertEquals($result, $secondResult);
    }

    public function testGetSchemaFile()
    {
        $etcDir = str_replace('/', DIRECTORY_SEPARATOR, 'app/code/Mage/Core/etc');
        $expectedDir = str_replace('/', DIRECTORY_SEPARATOR, 'app/code/Mage/Core/etc/service_calls.xsd');
        $this->_modulesReaderMock->expects($this->any())->method('getModuleDir')
            ->with('etc', 'Mage_Core')
            ->will($this->returnValue($etcDir));
        $result = $this->_configReader->getSchemaFile();
        $this->assertNotNull($result);
        $this->assertEquals($expectedDir, $result, 'returned schema file is wrong');
    }
}

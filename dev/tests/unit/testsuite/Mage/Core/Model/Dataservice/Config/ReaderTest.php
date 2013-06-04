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
    protected $_configReader;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_modulesReaderMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_dirMock;

    public function setup()
    {
        $this->_modulesReaderMock = $this->getMockBuilder('Mage_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_modulesReaderMock->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->with('service_calls.xml')
            ->will($this->returnValue(array(__DIR__ . '/../_files/service_calls.xml',
                                            __DIR__ . '/../_files/second_service_calls.xml')));

        $this->_dirMock = $this->getMockBuilder('Mage_Core_Model_Dir')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configReader =
            new Mage_Core_Model_Dataservice_Config_Reader($this->_modulesReaderMock, $this->_dirMock);
    }

    public function testGetServiceCallConfig()
    {
        $result = $this->_configReader->getServiceCallConfig();
        $this->assertNotNull($result);
        $this->assertContains('name="alias"', $result, 'Does not contain call from service_calls.xml');
        $this->assertContains('name="another_alias"', $result, 'Does not contain call from service_calls.xml');
    }

    public function testGetSchemaFile()
    {
        $appDir = '/some/directory';
        $this->_dirMock->expects($this->any())->method('getDir')->will($this->returnValue($appDir));
        $result = $this->_configReader->getSchemaFile();
        $this->assertNotNull($result);
        $this->assertEquals($appDir .'/code/Mage/Core/etc/service_calls.xsd', $result, 'returned schema file is wrong');
    }
}

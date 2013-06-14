<?php
/**
 * Test class for Mage_Core_Model_DataService_Config_Reader
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

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_modulesReaderMock;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_cacheTypes;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    private $_configLoader;

    /**
     * Prepare object manager with mocks of objects required by config reader.
     */
    public function setUp()
    {
        $this->_modulesReaderMock = $this->getMockBuilder('Mage_Core_Model_Config_Loader_Modules_File')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_cacheTypes = $this->getMockBuilder('Mage_Core_Model_Cache_Type_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configLoader = $this->getMockBuilder('Mage_Core_Model_DataService_Config_Loader')
            ->disableOriginalConstructor()
            ->getMock();

        $config = $this->getMockBuilder('Mage_Core_Model_Config_Base')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_configLoader->expects($this->any())
            ->method('getModulesConfig')
            ->will($this->returnValue($config));

        $this->_configReader = new Mage_Core_Model_DataService_Config_Reader(
                $this->_modulesReaderMock,
                $this->_cacheTypes,
                $this->_configLoader
            );
    }

    /**
     * Verify caching of config
     */
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

    /**
     * Verify correct schema file is returned.
     */
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

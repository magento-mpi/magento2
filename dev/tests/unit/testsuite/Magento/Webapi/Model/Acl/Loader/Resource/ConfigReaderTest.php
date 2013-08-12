<?php
/**
 * Test class for Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Loader_Resource_ConfigReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader
     */
    protected $_reader;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Config
     */
    protected $_configMock;

    /**
     * Initialize reader instance
     */
    protected function setUp()
    {
        $path = array(__DIR__, '..', '..', '..', '_files', 'acl.xml');
        $path = realpath(implode(DIRECTORY_SEPARATOR, $path));
        $dirPath = array(
            __DIR__, '..', '..', '..', '..', '..', '..', '..', '..', '..', '..', 'app', 'code', 'Mage', 'Webapi', 'etc'
        );
        $dirPath = realpath(implode(DIRECTORY_SEPARATOR, $dirPath));
        $fileListMock = $this->getMockBuilder('Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader_FileList')
            ->disableOriginalConstructor()
            ->getMock();
        $fileListMock->expects($this->any())->method('asArray')->will($this->returnValue(array($path)));
        $mapperMock = $this->getMock('Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper');
        $converterMock = $this->getMock('Magento_Config_Dom_Converter_ArrayConverter');
        $this->_configMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Webapi')
            ->will($this->returnValue($dirPath));

        $this->_reader = new Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader(
            $fileListMock,
            $mapperMock,
            $converterMock,
            $this->_configMock
        );
    }

    public function testGetSchemaFile()
    {
        $actualXsdPath = $this->_reader->getSchemaFile();
        $this->assertInternalType('string', $actualXsdPath);
        $this->assertFileExists($actualXsdPath);
    }

    public function testGetVirtualResources()
    {
        $resources = $this->_reader->getAclVirtualResources();
        $this->assertEquals(1, $resources->length, 'More than one virtual resource.');
        $this->assertEquals('customer/list', $resources->item(0)->getAttribute('id'), 'Wrong id of virtual resource');
        $this->assertEquals('customer/get', $resources->item(0)->getAttribute('parent'),
            'Wrong parent id of virtual resource');
    }
}

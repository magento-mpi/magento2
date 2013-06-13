<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_Resource_ConfigReader_XmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Acl_Loader_Resource_ConfigReader_Xml
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mapperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converterMock;

    public function setUp()
    {
        $files = array(
            realpath(__DIR__) . '/../../../_files/acl_1.xml',
            realpath(__DIR__) . '/../../../_files/acl_2.xml'
        );
        $fileListMock = $this->getMock('Magento_Acl_Loader_Resource_ConfigReader_FileListInterface');
        $fileListMock->expects($this->any())->method('asArray')->will($this->returnValue($files));

        $this->_mapperMock = new Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper();
        $this->_converterMock = new Magento_Config_Dom_Converter_ArrayConverter();
        $this->_model = new Magento_Acl_Loader_Resource_ConfigReader_Xml(
            $fileListMock,
            $this->_mapperMock,
            $this->_converterMock
        );
    }

    public function testGetAclResources()
    {
        $resources = $this->_model->getAclResources();
        $this->assertNotEmpty($resources);
    }

    public function testGetAclResourcesMergedCorrectly()
    {
        $expectedResources = include realpath(__DIR__) . '/../../../_files/acl_merged.php';

        $actualResources = $this->_model->getAclResources();
        $this->assertNotEmpty($actualResources);
        $this->assertEquals($expectedResources, $actualResources);
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists($this->_model->getSchemaFile());
    }
}

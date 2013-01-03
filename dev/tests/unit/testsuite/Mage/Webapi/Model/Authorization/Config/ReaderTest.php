<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Authorization_Config_Reader
     */
    protected $_reader;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Config
     */
    protected $_configMock;

    /**
     * Initialize reader instance
     */
    protected function setUp()
    {
        $path = array(__DIR__, '..', '..', '_files', 'acl.xml');
        $path = realpath(implode(DIRECTORY_SEPARATOR, $path));
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Mage_Webapi')
            ->will($this->returnValue(
                realpath(__DIR__ . '/../../../../../../../../../app/code/core/Mage/Webapi/etc'))
        );

        $this->_reader = new Mage_Webapi_Model_Authorization_Config_Reader($this->_configMock, array($path));
    }

    /**
     * Unset reader instance.
     */
    protected function tearDown()
    {
        unset($this->_reader);
        unset($this->_configMock);
    }

    /**
     * Check that correct XSD file is provided.
     */
    public function testGetSchemaFile()
    {
        $xsdPath = array(__DIR__, '..', '..', '_files', 'acl.xsd');
        $xsdPath = realpath(implode(DIRECTORY_SEPARATOR, $xsdPath));
        $actualXsdPath = $this->_reader->getSchemaFile();

        $this->assertInternalType('string', $actualXsdPath);
        $this->assertFileExists($actualXsdPath);
        $this->assertXmlFileEqualsXmlFile($xsdPath, $actualXsdPath);
    }
}

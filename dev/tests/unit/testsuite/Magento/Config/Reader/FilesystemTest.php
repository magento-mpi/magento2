<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Config_Reader_FilesystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Config_FileResolverInterface
     */
    protected $_fileResolverMock;

    /**
     * @var Magento_Config_ConverterInterface
     */
    protected $_converterMock;

    /**
     * @var string
     */
    protected $_file;

    protected function setUp()
    {
        $this->_file =  __DIR__ . '/../_files/reader/config.xml';
        $this->_fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $this->_converterMock = $this->getMock('Magento_Config_ConverterInterface', array(), array(), '', false);
    }

    public function testRead()
    {
        $model = new Magento_Config_Reader_Filesystem(
            $this->_fileResolverMock,
            $this->_converterMock,
            'fileName',
            array()
        );
        $this->_fileResolverMock
            ->expects($this->once())->method('get')->will($this->returnValue(array($this->_file)));

        $dom = new DomDocument();
        $dom->loadXML(file_get_contents($this->_file));
        $this->_converterMock->expects($this->once())->method('convert')->with($dom);
        $model->read('scope');
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Invalid Document
     */
    public function testReadWithInvalidDom()
    {
        $model = new Magento_Config_Reader_Filesystem(
            $this->_fileResolverMock,
            $this->_converterMock,
            'fileName',
            array(),
            __DIR__ . "/../_files/reader/schema.xsd",
            null,
            true
        );
        $this->_fileResolverMock
            ->expects($this->once())->method('get')->will($this->returnValue(array($this->_file)));

        $model->read('scope');
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Invalid XML in file
     */
    public function testReadWithInvalidXml()
    {
        $model = new Magento_Config_Reader_Filesystem(
            $this->_fileResolverMock,
            $this->_converterMock,
            'fileName',
            array(),
            null,
            __DIR__ . "/../_files/reader/schema.xsd",
            true
        );
        $this->_fileResolverMock
            ->expects($this->once())->method('get')->will($this->returnValue(array($this->_file)));
        $model->read('scope');
    }
}

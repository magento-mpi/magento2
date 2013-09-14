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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolverMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_schemaLocatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validationStateMock;

    /**
     * @var string
     */
    protected $_file;

    protected function setUp()
    {
        $this->_file =  __DIR__ . '/../_files/reader/config.xml';
        $this->_fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $this->_converterMock = $this->getMock('Magento\Config\ConverterInterface', array(), array(), '', false);
        $this->_schemaLocatorMock = $this->getMock(
            'Magento\Core\Model\Module\Declaration\SchemaLocator', array(), array(), '', false
        );
        $this->_validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
    }

    public function testRead()
    {
        $model = new \Magento\Config\Reader\Filesystem(
            $this->_fileResolverMock,
            $this->_converterMock,
            $this->_schemaLocatorMock,
            $this->_validationStateMock,
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
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Invalid Document
     */
    public function testReadWithInvalidDom()
    {
        $this->_schemaLocatorMock->expects($this->once())
            ->method('getSchema')
            ->will($this->returnValue(__DIR__ . "/../_files/reader/schema.xsd"));
        $this->_validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));
        $model = new \Magento\Config\Reader\Filesystem(
            $this->_fileResolverMock,
            $this->_converterMock,
            $this->_schemaLocatorMock,
            $this->_validationStateMock,
            'fileName',
            array()
        );
        $this->_fileResolverMock
            ->expects($this->once())->method('get')->will($this->returnValue(array($this->_file)));

        $model->read('scope');
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Invalid XML in file
     */
    public function testReadWithInvalidXml()
    {
        $this->_schemaLocatorMock->expects($this->any())
            ->method('getPerFileSchema')
            ->will($this->returnValue(__DIR__ . "/../_files/reader/schema.xsd"));
        $this->_validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));

        $model = new \Magento\Config\Reader\Filesystem(
            $this->_fileResolverMock,
            $this->_converterMock,
            $this->_schemaLocatorMock,
            $this->_validationStateMock,
            'fileName',
            array()
        );
        $this->_fileResolverMock
            ->expects($this->once())->method('get')->will($this->returnValue(array($this->_file)));
        $model->read('scope');
    }
}

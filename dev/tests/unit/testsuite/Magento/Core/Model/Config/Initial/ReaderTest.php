<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Initial;

use Magento\App\Filesystem;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Initial\Reader
     */
    protected $_model;

    /**
     * @var \Magento\Config\FileResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolverMock;

    /**
     * @var \Magento\Core\Model\Config\Initial\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converterMock;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    protected function setUp()
    {
        $this->_filePath = __DIR__ . '/_files/';
        $this->_fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $this->_converterMock = $this->getMock('Magento\Core\Model\Config\Initial\Converter');
        $schemaLocatorMock =
            $this->getMock('Magento\Core\Model\Config\Initial\SchemaLocator', array(), array(), '', false);
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->once())->method('isValidated')->will($this->returnValue(true));
        $schemaFile = $this->_filePath . 'config.xsd';
        $schemaLocatorMock->expects($this->once())->method('getSchema')->will($this->returnValue($schemaFile));
        $this->rootDirectory = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array('readFile', 'getRelativePath'),
            array(), '', false
        );
        $this->_model = new \Magento\Core\Model\Config\Initial\Reader(
            $this->_fileResolverMock,
            $this->_converterMock,
            $schemaLocatorMock,
            $validationStateMock
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\Initial\Reader::read
     */
    public function testReadNoFiles()
    {
        $this->_fileResolverMock->expects($this->at(0))
            ->method('get')
            ->with('config.xml', 'primary')
            ->will($this->returnValue(array()));

        $this->_fileResolverMock->expects($this->at(1))
            ->method('get')
            ->with('config.xml', 'global')
            ->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->_model->read());
    }

    /**
     * @covers \Magento\Core\Model\Config\Initial\Reader::read
     */
    public function testReadValidConfig()
    {
        $testXmlFilesList = array(
            file_get_contents($this->_filePath . 'initial_config1.xml'),
            file_get_contents($this->_filePath . 'initial_config2.xml')
        );
        $expectedConfig = array(
            'data' => array(),
            'metadata' => array()
        );

        $this->_fileResolverMock->expects($this->at(0))
            ->method('get')
            ->with('config.xml', 'primary')
            ->will($this->returnValue(array()));

        $this->_fileResolverMock->expects($this->at(1))
            ->method('get')
            ->with('config.xml', 'global')
            ->will($this->returnValue($testXmlFilesList));

        $this->_converterMock->expects($this->once())
            ->method('convert')
            ->with($this->anything())
            ->will($this->returnValue($expectedConfig));

        $this->rootDirectory->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));

        $this->rootDirectory->expects($this->any())
            ->method('readFile')
            ->will($this->returnValue('<config></config>'));

        $this->assertEquals($expectedConfig, $this->_model->read());
    }
}

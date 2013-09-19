<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Initial;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Initial\Reader
     */
    protected $_model;

    /**
     * @var \Magento\Config\FileResolverInterface
     */
    protected $_fileResolverMock;

    /**
     * @var \Magento\Core\Model\Config\Initial\Converter
     */
    protected $_converterMock;

    /**
     * @var string
     */
    protected $_filePath;

    protected function setUp()
    {
        $this->_filePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $this->_converterMock = $this->getMock('Magento\Core\Model\Config\Initial\Converter');

        $this->_model = new \Magento\Core\Model\Config\Initial\Reader(
            $this->_fileResolverMock,
            $this->_converterMock
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
            $this->_filePath . 'initial_config1.xml',
            $this->_filePath . 'initial_config2.xml'
        );
        $expectedConfig = include ($this->_filePath . 'initial_config_merged.php');

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

        $this->assertEquals($expectedConfig, $this->_model->read());
    }
}

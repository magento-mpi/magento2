<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Locale\Hierarchy\Config;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Locale\Hierarchy\Config\FileResolver
     */
    protected $_model;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $_directoryMock;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    protected function setUp()
    {
        $filesystem = $this->getMock('Magento\Framework\App\Filesystem', array('getDirectoryRead'), array(), '', false);
        $this->_directoryMock = $this->getMock(
            '\Magento\Framework\Filesystem\Directory\Read',
            array('isExist', 'search'),
            array(),
            '',
            false
        );
        $filesystem->expects(
            $this->once()
        )->method(
            'getDirectoryRead'
        )->with(
            \Magento\Framework\App\Filesystem::APP_DIR
        )->will(
            $this->returnValue($this->_directoryMock)
        );
        $this->_directoryMock->expects(
            $this->once()
        )->method(
            'isExist'
        )->with(
            'locale'
        )->will(
            $this->returnValue(true)
        );
        $this->iteratorFactory = $this->getMock(
            'Magento\Framework\Config\FileIteratorFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_model = new \Magento\Framework\Locale\Hierarchy\Config\FileResolver(
            $filesystem,
            $this->iteratorFactory
        );
    }

    /**
     * @covers \Magento\Framework\Locale\Hierarchy\Config\FileResolver::get
     */
    public function testGet()
    {
        $paths = array(
            __DIR__ . '/_files/custom/hierarchy_config.xml',
            __DIR__ . '/_files/default/hierarchy_config.xml'
        );
        $expected = array(0 => $paths);

        $this->_directoryMock->expects($this->once())->method('search')->will($this->returnValue(array($paths)));
        $this->iteratorFactory->expects($this->once())->method('create')->will($this->returnValue(array($paths)));
        $this->assertEquals($expected, $this->_model->get('hierarchy_config.xml', 'scope'));
    }
}

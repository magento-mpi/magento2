<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Arguments;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loader
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    public function setUp()
    {
        $this->_dirs = $this->getMock(
            '\Magento\Framework\App\Filesystem\DirectoryList',
            array('getPath'),
            array(),
            '',
            false
        );
    }

    public function testWithOnePhpFile()
    {
        $this->_dirs->expects($this->once())->method('getPath')->will($this->returnValue(__DIR__ . '/_files'));
        $this->_model = new Loader($this->_dirs);
        $expected = require __DIR__ . '/_files/config.php';
        $this->assertEquals($expected, $this->_model->load());
    }

    public function testWithoutPhpFiles()
    {
        $this->_dirs->expects($this->once())->method('getPath')->will($this->returnValue(__DIR__ . '/notExistFolder'));
        $this->_model = new Loader($this->_dirs);
        $this->assertEquals(array(), $this->_model->load());
    }
}

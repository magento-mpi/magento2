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

    public function testWithOneXmlFile()
    {
        $this->_dirs->expects($this->once())->method('getPath')->will($this->returnValue(__DIR__ . '/_files'));
        $this->_model = new Loader($this->_dirs);
        $expected = require __DIR__ . '/_files/local.php';
        $this->assertEquals($expected, $this->_model->load());
    }

    public function testWithTwoXmlFileMerging()
    {
        $this->_dirs->expects($this->once())->method('getPath')->will($this->returnValue(__DIR__ . '/_files'));
        $this->_model = new Loader($this->_dirs, 'other/local_developer.xml');
        $expected = require __DIR__ . '/_files/other/local_developer_merged.php';
        $this->assertEquals($expected, $this->_model->load());
    }

    public function testWithoutXmlFiles()
    {
        $this->_dirs->expects($this->once())->method('getPath')->will($this->returnValue(__DIR__ . '/notExistFolder'));
        $this->_model = new Loader($this->_dirs);
        $this->assertEquals(array(), $this->_model->load());
    }
}

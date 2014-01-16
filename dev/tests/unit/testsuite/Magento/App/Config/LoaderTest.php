<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Config;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loader
     */
    protected $_model;

    /**
     * @var \Magento\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    public function setUp()
    {
        $this->_dirs = $this->getMock('\Magento\Filesystem\DirectoryList', array('getDir'), array(), '', false);
    }

    public function testWithOneXmlFile()
    {
        $this->_dirs->expects($this->once())->method('getDir')->will($this->returnValue(__DIR__ . '/_files'));
        $this->_model = new Loader($this->_dirs);
        $expected = array(
            'resource' => 'resource name',
            'connection' => 'connection name',
            'other' => 'other value',
        );
        $this->assertEquals($expected, $this->_model->load());
    }

    public function testWithTwoXmlFileMerging()
    {
        $this->_dirs->expects($this->once())->method('getDir')->will($this->returnValue(__DIR__ . '/_files'));
        $this->_model = new Loader($this->_dirs, 'other/local_developer.xml');
        $expected = array(
            'resource' => 'resource name2',
            'connection' => 'connection name2',
            'other' => 'new other value',
            'new' => 'new value',
        );
        $this->assertEquals($expected, $this->_model->load());
    }

    public function testWithoutXmlFiles()
    {
        $this->_dirs->expects($this->once())->method('getDir')->will($this->returnValue(__DIR__ . '/notExistFolder'));
        $this->_model = new Loader($this->_dirs);
        $this->assertEquals(array(), $this->_model->load());
    }
}

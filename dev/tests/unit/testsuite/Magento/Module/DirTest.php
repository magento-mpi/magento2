<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

class DirTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Module\Dir
     */
    protected $_model;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Stdlib\String|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stringMock;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryMock;

    protected function setUp()
    {
        $this->filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false, false);
        $this->directoryMock  = $this->getMock('Magento\Filesystem\Directory\Read', array(), array(), '', false, false);
        $this->_stringMock    = $this->getMock('Magento\Stdlib\String', array(), array(), '', false, false);

        $this->_stringMock->expects($this->once())
            ->method('upperCaseWords')
            ->will($this->returnValue('Test/Module'));

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->will($this->returnValue($this->directoryMock));

        $this->_model = new \Magento\Module\Dir($this->filesystemMock, $this->_stringMock);
    }

    public function testGetDirModuleRoot()
    {
        $this->directoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with('Test/Module')
            ->will($this->returnValue('/Test/Module'));
        $this->assertEquals(
            '/Test/Module',
            $this->_model->getDir('Test_Module')
        );
    }

    public function testGetDirModuleSubDir()
    {
        $this->directoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with('Test/Module/etc')
            ->will($this->returnValue('/Test/Module/etc'));
        $this->assertEquals(
            '/Test/Module/etc',
            $this->_model->getDir('Test_Module', 'etc')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Directory type 'unknown' is not recognized
     */
    public function testGetDirModuleSubDirUnknown()
    {
        $this->_model->getDir('Test_Module', 'unknown');
    }
}

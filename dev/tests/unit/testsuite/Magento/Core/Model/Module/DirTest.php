<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Module;

class DirTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Module\Dir
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Dir|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationDirs;

    protected function setUp()
    {
        $this->_applicationDirs = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false, false);
        $this->_applicationDirs
            ->expects($this->once())
            ->method('getDir')
            ->with(\Magento\Core\Model\Dir::MODULES)
            ->will($this->returnValue('app' . DS .'code'))
        ;
        $this->_model = new \Magento\Core\Model\Module\Dir($this->_applicationDirs);
    }

    public function testGetDirModuleRoot()
    {
        $this->assertEquals('app' . DS .'code' . DS .'Test' . DS .'Module', $this->_model->getDir('Test_Module'));
    }

    public function testGetDirModuleSubDir()
    {
        $this->assertEquals('app' . DS .'code' . DS .'Test' . DS .'Module' . DS .'etc',
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

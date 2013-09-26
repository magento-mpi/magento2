<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Module_DirTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Module_Dir
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationDirs;

    protected function setUp()
    {
        $this->_applicationDirs = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false, false);
        $this->_applicationDirs
            ->expects($this->once())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::MODULES)
            ->will($this->returnValue('app/code'))
        ;
        $this->_model = new Magento_Core_Model_Module_Dir($this->_applicationDirs);
    }

    public function testGetDirModuleRoot()
    {
        $this->assertEquals('app/code/Test/Module', $this->_model->getDir('Test_Module'));
    }

    public function testGetDirModuleSubDir()
    {
        $this->assertEquals('app/code/Test/Module/etc', $this->_model->getDir('Test_Module', 'etc'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Directory type 'unknown' is not recognized
     */
    public function testGetDirModuleSubDirUnknown()
    {
        $this->_model->getDir('Test_Module', 'unknown');
    }
}

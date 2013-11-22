<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\App\Action\Plugin;

class DirTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\App\Action\Plugin\Dir
     */
    protected $_plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    protected function setUp()
    {
        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $this->_plugin = new \Magento\Install\App\Action\Plugin\Dir(
            $this->_appStateMock,
            $this->_dirMock
        );
    }

    public function testBeforeDispatchWhenAppIsInstalled()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_dirMock
            ->expects($this->once())
            ->method('getDir')
            ->with(\Magento\App\Dir::VAR_DIR)->will($this->returnValue('dir_name'));
        $this->assertEquals(array(), $this->_plugin->beforeDispatch(array()));
    }

    public function testBeforeDispatchWhenAppIsNotInstalled()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_dirMock->expects($this->never())->method('getDir');
        $this->assertEquals(array(), $this->_plugin->beforeDispatch(array()));
    }
}
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
     * Dir plugin
     *
     * @var \Magento\Install\App\Action\Plugin\Dir
     */
    protected $plugin;

    /**
     * App state mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\State
     */
    protected $appStateMock;

    /**
     * Var directory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\Write
     */
    protected $varDirectory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    protected function setUp()
    {
        $this->appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $filesystem = $this->getMock('Magento\App\Filesystem', array('getDirectoryWrite'), array(), '', false);
        $this->varDirectory = $this->getMock(
            'Magento\Filesystem\Directory\Write', array('read', 'isDirectory', 'delete'), array(), '', false
        );
        $filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::VAR_DIR)
            ->will($this->returnValue($this->varDirectory));
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\Install\Controller\Index', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->plugin = new \Magento\Install\App\Action\Plugin\Dir(
            $this->appStateMock,
            $filesystem,
            $logger
        );
    }

    /**
     * Test when app is installed
     */
    public function testBeforeDispatchWhenAppIsInstalled()
    {
        $directories = array('dir1', 'dir2');
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->varDirectory->expects($this->once())
            ->method('read')
            ->will($this->returnValue($directories));
        $this->varDirectory->expects($this->exactly(count($directories)))
            ->method('isDirectory')
            ->will($this->returnValue(true));
        $this->varDirectory->expects($this->exactly(count($directories)))
            ->method('delete');
        $this->plugin->beforeDispatch($this->subjectMock, $this->requestMock);
    }

    /**
     * Test when app is not installed
     */
    public function testBeforeDispatchWhenAppIsNotInstalled()
    {
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->varDirectory->expects($this->never())->method('read');
        $this->plugin->beforeDispatch($this->subjectMock, $this->requestMock);
    }
}
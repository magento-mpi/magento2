<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\App\Http
     */
    protected $http;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontControllerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $areaListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getFrontName'])
            ->getMock();
        $this->areaListMock = $this->getMockBuilder('Magento\Framework\App\AreaList')
            ->disableOriginalConstructor()
            ->setMethods(['getCodeByFrontName'])
            ->getMock();
        $this->configLoaderMock = $this->getMockBuilder('Magento\Framework\App\ObjectManager\ConfigLoader')
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(['configure', 'get', 'create'])
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods(['setBody', '__wakeup', 'sendHeaders', 'sendResponse', 'setRedirect'])
            ->getMock();
        $this->frontControllerMock = $this->getMockBuilder('Magento\Framework\App\FrontControllerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
        $this->filesystemMock = $this->getMockBuilder('Magento\Framework\App\Filesystem')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();

        $this->http = $this->objectManager->getObject(
            'Magento\Framework\App\Http',
            [
                'objectManager' => $this->objectManagerMock,
                'eventManager' => $this->eventManagerMock,
                'areaList' => $this->areaListMock,
                'request' => $this->requestMock,
                'response' => $this->responseMock,
                'configLoader' => $this->configLoaderMock,
                'filesystem' => $this->filesystemMock
            ]
        );
    }

    /**
     * Asserts mock objects with methods that are expected to be called when http->launch() is invoked.
     */
    private function setUpLaunch()
    {
        $frontName = 'frontName';
        $areaCode = 'areaCode';
        $this->requestMock->expects($this->once())->method('getFrontName')->will($this->returnValue($frontName));
        $this->areaListMock->expects($this->once())
            ->method('getCodeByFrontName')
            ->with($frontName)->will($this->returnValue($areaCode));
        $this->configLoaderMock->expects($this->once())
            ->method('load')->with($areaCode)->will($this->returnValue([]));
        $this->objectManagerMock->expects($this->once())->method('configure')->with([]);
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\App\FrontControllerInterface')
            ->will($this->returnValue($this->frontControllerMock));
        $this->frontControllerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->requestMock)
            ->will($this->returnValue($this->responseMock));
    }

    public function testLaunchSuccess()
    {
        $this->setUpLaunch();
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with(
                'controller_front_send_response_before',
                array('request' => $this->requestMock, 'response' => $this->responseMock)
            );
        $this->assertSame($this->responseMock, $this->http->launch());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Message
     */
    public function testLaunchException()
    {
        $this->setUpLaunch();
        $this->frontControllerMock->expects($this->once())->method('dispatch')->with($this->requestMock)->will(
            $this->returnCallback(
                function () {
                    throw new \Exception('Message');
                }
            )
        );
        $this->http->launch();
    }

    public function testNotInstalledException()
    {
        $expectedException = new \Exception('Test Message');
        $bootstrapMock = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $bootstrapMock->expects($this->once())->method('isDeveloperMode')->willReturn(true);
        $bootstrapMock->expects($this->once())->method('getErrorCode')->willReturn(Bootstrap::ERR_IS_INSTALLED);

        $path = $this->http->getInstallerRedirectPath([]);
        $this->responseMock->expects($this->once())->method('setRedirect')->with($path)->will($this->returnSelf());
        $this->responseMock->expects($this->once())->method('sendHeaders')->will($this->returnSelf());
        $this->assertTrue($this->http->catchException($bootstrapMock, $expectedException));
    }
}

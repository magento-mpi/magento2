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
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var \Magento\Framework\App\Http
     */
    protected $_http;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontControllerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_requestMock = $this->getMockBuilder(
            'Magento\Framework\App\Request\Http'
        )->disableOriginalConstructor()->setMethods(['getFrontName'])->getMock();
        $frontName = 'frontName';
        $this->_requestMock->expects($this->once())->method('getFrontName')->will($this->returnValue($frontName));
        $areaCode = 'areaCode';
        $areaListMock = $this->getMockBuilder('Magento\Framework\App\AreaList')
            ->disableOriginalConstructor()
            ->setMethods(['getCodeByFrontName'])
            ->getMock();
        $areaListMock->expects($this->once())->method('getCodeByFrontName')->with($frontName)->will(
            $this->returnValue($areaCode)
        );
        $this->_stateMock = $this->getMockBuilder('Magento\Framework\App\State')
            ->disableOriginalConstructor()
            ->setMethods(['setAreaCode', 'getMode'])
            ->getMock();
        $this->_stateMock->expects($this->once())->method('setAreaCode')->with($areaCode);
        $areaConfig = [];
        $configLoaderMock = $this->getMockBuilder(
            'Magento\Framework\App\ObjectManager\ConfigLoader'
        )->disableOriginalConstructor()->setMethods(['load'])->getMock();
        $configLoaderMock->expects($this->once())->method('load')->with($areaCode)->will(
            $this->returnValue($areaConfig)
        );
        $objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManager')->disableOriginalConstructor()->setMethods(
            ['configure', 'get', 'create']
        )->getMock();
        $objectManagerMock->expects($this->once())->method('configure')->with($areaConfig);
        $this->_responseMock = $this->getMockBuilder(
            'Magento\Framework\App\Response\Http'
        )->disableOriginalConstructor()->setMethods(
            ['setHttpResponseCode', 'setBody']
        )->getMock();
        $this->_frontControllerMock = $this->getMockBuilder(
            'Magento\Framework\App\FrontControllerInterface'
        )->disableOriginalConstructor()->setMethods(['dispatch'])->getMock();
        $objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\App\FrontControllerInterface')
            ->will($this->returnValue($this->_frontControllerMock));
        $this->_frontControllerMock->expects($this->once())->method('dispatch')->with($this->_requestMock)->will(
            $this->returnValue($this->_responseMock)
        );
        $this->_eventManagerMock = $this->getMockBuilder(
            'Magento\Framework\Event\Manager'
        )->disableOriginalConstructor()->setMethods(
            ['dispatch']
        )->getMock();
        $this->_filesystemMock = $this->getMockBuilder(
            'Magento\Framework\App\Filesystem'
        )->disableOriginalConstructor()->setMethods(
            ['getPath']
        )->getMock();

        $this->_http = $this->_objectManager->getObject(
            'Magento\Framework\App\Http',
            [
                'objectManager' => $objectManagerMock,
                'eventManager' => $this->_eventManagerMock,
                'areaList' => $areaListMock,
                'request' => $this->_requestMock,
                'response' => $this->_responseMock,
                'configLoader' => $configLoaderMock,
                'state' => $this->_stateMock,
                'filesystem' => $this->_filesystemMock
            ]
        );
    }
    
    public function testLaunchSuccess()
    {
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with(
            'controller_front_send_response_before',
            array('request' => $this->_requestMock, 'response' => $this->_responseMock)
        );
        $this->assertSame($this->_responseMock, $this->_http->launch());
    }

    public function testLaunchDispatchException()
    {
        $this->_frontControllerMock->expects($this->once())->method('dispatch')->with($this->_requestMock)->will(
            $this->returnCallback(
                function () {
                    throw new \Exception('Message');
                }
            )
        );
        $this->_stateMock->expects($this->once())->method('getMode')->will(
            $this->returnValue(\Magento\Framework\App\State::MODE_DEVELOPER)
        );
        $this->_responseMock->expects($this->once())->method('setHttpResponseCode')->with(500);

        $this->_responseMock->expects($this->once())->method('setBody')->with(
            $this->matchesRegularExpression('/Message[\n]+<pre>Message[\n]*(.|\n)*<\/pre>/')
        );
        $this->assertSame($this->_responseMock, $this->_http->launch());
    }
}

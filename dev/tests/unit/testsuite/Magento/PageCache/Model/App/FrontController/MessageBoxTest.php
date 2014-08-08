<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class MessageBoxTest
 */
class MessageBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Version instance
     *
     * @var MessageBox
     */
    protected $msgBox;

    /**
     * Cookie mock
     *
     * @var \Magento\Framework\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieManagerMock;

    /**
     * Cookie mock
     *
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $publicCookieMetadataMock;

    /**
     * Cookie mock
     *
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMetadataFactoryMock;

    /**
     * Request mock
     *
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\App\FrontController
     */
    protected $objectMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $responseMock;

    public function setUp()
    {
        $this->cookieManagerMock = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cookieMetadataFactoryMock = $this->getMockBuilder(
            'Magento\Framework\Stdlib\Cookie\CookieMetadataFactory'
        )->disableOriginalConstructor()
            ->getMock();
        $this->publicCookieMetadataMock = $this->getMockBuilder(
            'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata'
        )->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockBuilder('Magento\Framework\Message\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->msgBox = (new ObjectManager($this))->getObject(
            'Magento\PageCache\Model\App\FrontController\MessageBox',
            [
                'cookieManager' => $this->cookieManagerMock,
                'cookieMetadataFactory' => $this->cookieMetadataFactoryMock,
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
            ]
        );

        $this->objectMock = $this->getMock('Magento\Framework\App\FrontController', array(), array(), '', false);
        $this->responseMock = $this->getMock('Magento\Framework\App\ResponseInterface', array(), array(), '', false);
    }

    /**
     * @param bool $isPost
     * @param int $numOfCalls
     * @dataProvider afterDispatchTestDataProvider
     */
    public function testAfterDispatch($isPost, $numOfCalls)
    {
        $this->messageManagerMock->expects($this->exactly($numOfCalls))
            ->method('hasMessages')
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue($isPost));
        $this->cookieMetadataFactoryMock->expects($this->exactly($numOfCalls))
            ->method('createPublicCookieMetadata')
            ->will($this->returnValue($this->publicCookieMetadataMock));
        $this->publicCookieMetadataMock->expects(($this->exactly($numOfCalls)))
            ->method('setDuration')
            ->with(MessageBox::COOKIE_PERIOD)
            ->will($this->returnValue($this->publicCookieMetadataMock));
        $this->publicCookieMetadataMock->expects(($this->exactly($numOfCalls)))
            ->method('setPath')
            ->with('/')
            ->will($this->returnValue($this->publicCookieMetadataMock));
        $this->cookieManagerMock->expects($this->exactly($numOfCalls))
            ->method('setPublicCookie')
            ->with(
                MessageBox::COOKIE_NAME,
                1,
                $this->publicCookieMetadataMock
            );
        $this->assertEquals($this->responseMock, $this->msgBox->afterDispatch($this->objectMock, $this->responseMock));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function afterDispatchTestDataProvider()
    {
        return [
            [true, 1],
            [false, 0],
        ];
    }
}

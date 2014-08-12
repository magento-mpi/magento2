<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sendfriend\Model;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Test Sendfriend
 *
 */
class SendfriendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sendfriend\Model\Sendfriend
     */
    protected $modelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\CookieManager
     */
    protected $cookieManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendfriendDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $remoteAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;


    public function setUp()
    {

        $objectManager = new ObjectManager($this);
        $this->sendfriendDataMock = $this->getMockBuilder('Magento\Sendfriend\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cookieManagerMock = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManager')
            ->disableOriginalConstructor()->getMock();
        $this->storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->remoteAddressMock = $this->getMockBuilder('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress')
            ->disableOriginalConstructor()->getMock();

        $this->modelMock = $objectManager->getObject(
            'Magento\Sendfriend\Model\Sendfriend',
            [
                'sendfriendData' => $this->sendfriendDataMock,
                'cookieManager' => $this->cookieManagerMock,
                'storeManager' => $this->storeManagerMock,
                'remoteAddress' => $this->remoteAddressMock,
            ]
        );

    }

    public function testGetSentCountWithCheckCookie()
    {
        $cookieName = 'testCookieName';
        $this->sendfriendDataMock->expects($this->once())->method('getLimitBy')->with()->will(
            $this->returnValue(\Magento\Sendfriend\Helper\Data::CHECK_COOKIE)
        );
        $this->sendfriendDataMock->expects($this->once())->method('getCookieName')->with()->will(
            $this->returnValue($cookieName)
        );

        $this->cookieManagerMock->expects($this->once())->method('getCookie')->with($cookieName)->will(
            $this->returnValue(30)
        );
        $this->assertEquals(0, $this->modelMock->getSentCount());
    }
}

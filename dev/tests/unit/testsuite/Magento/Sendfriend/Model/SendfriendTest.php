<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sendfriend\Model;

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

    public function setUp()
    {
        $this->modelMock = $this->getMockBuilder('Magento\Sendfriend\Model\Sendfriend')
            ->setMethods(['setData', '_getData', '__wakeup'])
            ->disableOriginalConstructor()->getMock();

        $this->cookieManagerMock = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManager')
            ->disableOriginalConstructor()->getMock();
    }

    public function testSetCookieManager()
    {
        $this->modelMock->expects($this->once())
            ->method('setData')
            ->with('_cookie_manager', $this->cookieManagerMock);

        $this->modelMock->setCookieManager($this->cookieManagerMock);
    }

    public function testGetCookieManager()
    {
        $this->modelMock->expects($this->once())
            ->method('_getData')
            ->with('_cookie_manager')->will($this->returnValue($this->cookieManagerMock));
        $result = $this->modelMock->getCookieManager();
        $this->assertSame($this->cookieManagerMock, $result);
    }

    public function testGetCookieManagerWithException()
    {
        try {
            $this->modelMock->expects($this->once())
                ->method('_getData')
                ->with('_cookie_manager')->will($this->returnValue(null));
            $this->modelMock->getCookieManager();
            $this->fail('Failed to model exception');
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->assertEquals(
                'Please define a correct CookieManager instance.',
                $e->getMessage()
            );
        }
    }
}

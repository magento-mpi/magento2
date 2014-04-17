<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\App\Action;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\App\Action\ContextPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Framework\App\Http\Context $httpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\Framework\App\Request\Http $httpRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpRequestMock;

    /**
     * @var \Magento\Store\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Directory\Model\Currency|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currencyMock;

    /**
     * @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $websiteMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->sessionMock = $this->getMock('Magento\Framework\Session\Generic',
            array('getCurrencyCode'), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\Framework\App\Http\Context',
            array(), array(), '', false);
        $this->httpRequestMock = $this->getMock('Magento\Framework\App\Request\Http',
            array('getCookie', 'getParam'), array(), '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager',
            array('getWebsite', '__wakeup'), array(), '', false);
        $this->storeMock = $this->getMock('Magento\Store\Model\Store',
            array('__wakeup', 'getDefaultCurrency'), array(), '', false);
        $this->currencyMock = $this->getMock('Magento\Directory\Model\Currency',
            array('getCode', '__wakeup'), array(), '', false);
        $this->websiteMock = $this->getMock('Magento\Store\Model\Website',
            array('getDefaultStore', '__wakeup'), array(), '', false);
        $this->closureMock = function () {
            return 'ExpectedValue';
        };
        $this->subjectMock = $this->getMock('Magento\Framework\App\Action\Action', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->plugin = new \Magento\Core\Model\App\Action\ContextPlugin(
            $this->sessionMock,
            $this->httpContextMock,
            $this->httpRequestMock,
            $this->storeManagerMock
        );
    }

    /**
     * Test aroundDispatch
     */
    public function testAroundDispatch()
    {
        $this->storeManagerMock->expects($this->exactly(2))
            ->method('getWebsite')
            ->will($this->returnValue($this->websiteMock));
        $this->websiteMock->expects($this->exactly(2))
            ->method('getDefaultStore')
            ->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())
            ->method('getDefaultCurrency')
            ->will($this->returnValue($this->currencyMock));
        $this->currencyMock->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('UAH'));
        $this->sessionMock->expects($this->once())
            ->method('getCurrencyCode')
            ->will($this->returnValue('UAH'));

        $this->httpRequestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('___store'))
            ->will($this->returnValue('default'));

        $this->httpRequestMock->expects($this->once())
            ->method('getCookie')
            ->with($this->equalTo(\Magento\Store\Model\Store::COOKIE_NAME))
            ->will($this->returnValue(null));

        $this->httpContextMock->expects($this->atLeastOnce())
            ->method('setValue')
            ->will($this->returnValueMap(array(
                array(\Magento\Core\Helper\Data::CONTEXT_CURRENCY, 'UAH', 'UAH', $this->httpContextMock),
                array(\Magento\Core\Helper\Data::CONTEXT_STORE, 'default', 'default', $this->httpContextMock),
            )));
        $this->assertEquals(
            'ExpectedValue',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}

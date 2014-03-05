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

namespace Magento\Core\Model\App;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\App\ContextPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Session\SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\App\Http\Context $httpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\App\Request\Http $httpRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpRequestMock;

    /**
     * @var \Magento\LauncherInterface
     */
    protected $launcherMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->sessionMock = $this->getMock('Magento\Core\Model\Session',
            array('getCurrencyCode'), array(), '', false);
        $this->launcherMock = $this->getMock('Magento\App\Http',
            array(), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context',
            array(), array(), '', false);
        $this->httpRequestMock = $this->getMock('Magento\App\Request\Http',
            array(), array(), '', false);
        $this->plugin = new \Magento\Core\Model\App\ContextPlugin(
            $this->sessionMock,
            $this->httpContextMock,
            $this->httpRequestMock
        );
    }

    /**
     * Test beforeLaunch
     */
    public function testBeforeLaunch()
    {
        $this->sessionMock->expects($this->once())
            ->method('getCurrencyCode')
            ->will($this->returnValue('UAH'));
        $this->httpRequestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('__store'))
            ->will($this->returnValue(0));
        $this->httpRequestMock->expects($this->once())
            ->method('getCookie')
            ->with($this->equalTo(\Magento\Core\Model\Store::COOKIE_NAME))
            ->will($this->returnValue(null));
        $this->httpContextMock->expects($this->atLeastOnce())
            ->method('setValue')
            ->will($this->returnValueMap(array(
                array(\Magento\Core\Helper\Data::CONTEXT_CURRENCY, 'UAH', $this->httpContextMock),
                array(\Magento\Core\Helper\Data::CONTEXT_STORE, 0, $this->httpContextMock),
            )));
        $this->assertNull($this->plugin->beforeLaunch($this->launcherMock));
    }
}

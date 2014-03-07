<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\App;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContextPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Catalog\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\LauncherInterface
     */
    protected $launcherMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->sessionMock = $this->getMock(
            '\Magento\Catalog\Model\Session',
            array(
                'hasSortDirection',
                'getSortDirection',
                'hasSortOrder',
                'getSortOrder',
                'hasDisplayMode',
                'getDisplayMode',
                'hasLimitPage',
                'getLimitPage'
            ),
            array(),
            '',
            false
        );
        $this->launcherMock = $this->getMock('Magento\App\Http', array(), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->plugin = new ContextPlugin($this->sessionMock, $this->httpContextMock);
    }

    public function testBeforeLaunchHasSortDirection()
    {
        $this->sessionMock->expects($this->once())
            ->method('hasSortDirection')
            ->will($this->returnValue(true));
        $this->sessionMock->expects($this->once())
            ->method('getSortDirection');
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_SORT_DIRECTION);
        $this->plugin->beforeLaunch($this->launcherMock);
    }

    public function testBeforeLaunchHasSortOrder()
    {
        $this->sessionMock->expects($this->once())
            ->method('hasSortOrder')
            ->will($this->returnValue(true));
        $this->sessionMock->expects($this->once())
            ->method('getSortOrder');
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_SORT_ORDER);
        $this->plugin->beforeLaunch($this->launcherMock);
    }

    public function testBeforeLaunchHasDisplayMode()
    {
        $this->sessionMock->expects($this->once())
            ->method('hasDisplayMode')
            ->will($this->returnValue(true));
        $this->sessionMock->expects($this->once())
            ->method('getDisplayMode');
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_DISPLAY_MODE);
        $this->plugin->beforeLaunch($this->launcherMock);
    }

    public function testBeforeLaunchHasLimitPage()
    {
        $this->sessionMock->expects($this->once())
            ->method('hasLimitPage')
            ->will($this->returnValue(true));
        $this->sessionMock->expects($this->once())
            ->method('getLimitPage');
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_LIMIT);
        $this->plugin->beforeLaunch($this->launcherMock);
    }
}

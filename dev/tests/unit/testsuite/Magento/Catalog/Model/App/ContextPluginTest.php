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
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $toolbarModelMock;

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
        $this->toolbarModelMock = $this->getMock(
            'Magento\Catalog\Model\Product\ProductList\Toolbar',
            array(
                'getDirection',
                'getOrder',
                'getMode',
                'getLimit'
            ),
            array(),
            '',
            false
        );
        $this->launcherMock = $this->getMock('Magento\App\Http', array(), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->plugin = new ContextPlugin($this->toolbarModelMock, $this->httpContextMock);
    }

    public function testBeforeLaunchHasSortDirection()
    {
        $this->toolbarModelMock->expects($this->exactly(2))
            ->method('getDirection')
            ->will($this->returnValue(true));
        $this->toolbarModelMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getLimit')
            ->will($this->returnValue(false));
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_SORT_DIRECTION, true);
        $this->plugin->beforeLaunch($this->launcherMock);
    }

    public function testBeforeLaunchHasSortOrder()
    {
        $this->toolbarModelMock->expects($this->exactly(2))
            ->method('getOrder')
            ->will($this->returnValue(true));
        $this->toolbarModelMock->expects($this->once())
            ->method('getDirection')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getLimit')
            ->will($this->returnValue(false));
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_SORT_ORDER, true);
        $this->plugin->beforeLaunch($this->launcherMock);
    }

    public function testBeforeLaunchHasDisplayMode()
    {
        $this->toolbarModelMock->expects($this->exactly(2))
            ->method('getMode')
            ->will($this->returnValue(true));
        $this->toolbarModelMock->expects($this->once())
            ->method('getDirection')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getLimit')
            ->will($this->returnValue(false));
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_DISPLAY_MODE, true);
        $this->plugin->beforeLaunch($this->launcherMock);
    }

    public function testBeforeLaunchHasLimitPage()
    {
        $this->toolbarModelMock->expects($this->exactly(2))
            ->method('getLimit')
            ->will($this->returnValue(true));
        $this->toolbarModelMock->expects($this->once())
            ->method('getDirection')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(false));
        $this->toolbarModelMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue(false));
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Helper\Data::CONTEXT_CATALOG_LIMIT, true);
        $this->plugin->beforeLaunch($this->launcherMock);
    }
}

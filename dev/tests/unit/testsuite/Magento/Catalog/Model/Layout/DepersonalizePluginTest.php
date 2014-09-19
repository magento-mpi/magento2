<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layout;

use Magento\TestFramework\Helper\ObjectManager;

class DepersonalizePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layout\DepersonalizePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Catalog\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogSessionMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheConfigMock;

    public function setUp()
    {
        $this->layoutMock = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $this->catalogSessionMock = $this->getMock('Magento\Catalog\Model\Session',
            ['clearStorage'],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->moduleManagerMock = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $this->cacheConfigMock = $this->getMock('Magento\PageCache\Model\Config', [], [], '', false);

        $this->plugin = (new ObjectManager($this))->getObject('Magento\Catalog\Model\Layout\DepersonalizePlugin', [
            'catalogSession' => $this->catalogSessionMock,
            'moduleManager' => $this->moduleManagerMock,
            'request' => $this->requestMock,
            'cacheConfig' => $this->cacheConfigMock
        ]);
    }

    public function testAfterGenerateXml()
    {
        $expectedResult = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $this->moduleManagerMock->expects($this->once())->method('isEnabled')->with('Magento_PageCache')
            ->willReturn(true);
        $this->cacheConfigMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->requestMock->expects($this->once($this->once()))->method('isAjax')->willReturn(false);
        $this->layoutMock->expects($this->once())->method('isCacheable')->willReturn(true);
        $this->catalogSessionMock->expects($this->once())->method('clearStorage');

        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertEquals($expectedResult, $actualResult);
    }
}

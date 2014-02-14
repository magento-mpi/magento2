<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\Layout;

class LayoutPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\Layout\LayoutPlugin
     */
    protected $model;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $responseMock;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $layoutMock;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $configMock;

    public function setUp()
    {
        $this->layoutMock = $this->getMockForAbstractClass(
            'Magento\Core\Model\Layout',
            [],
            '',
            false,
            true,
            true,
            ['isCacheable']
        );
        $this->responseMock = $this->getMock(
            '\Magento\App\Response\Http',
            ['setPublicHeaders', 'setNoCacheHeaders'],
            [],
            '',
            false
        );
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\App\ConfigInterface',
            [],
            '',
            false,
            true,
            true,
            ['isSetFlag', 'getValue']
        );

        $this->model = new \Magento\PageCache\Model\Layout\LayoutPlugin(
            $this->layoutMock,
            $this->responseMock,
            $this->configMock
        );
    }

    public function testAfterGenerateXmlLayoutIsCacheable()
    {
        $maxAge = 180;
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));
            $this->configMock->expects($this->once())
                ->method('getValue')
                ->with(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL)
                ->will($this->returnValue($maxAge));
            $this->responseMock->expects($this->once())
                ->method('setPublicHeaders')
                ->with($maxAge);
        $this->model->afterGenerateXml();
    }

    public function testAfterGenerateXmlLayoutIsNotCacheable()
    {
        $maxAge = 180;
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(false));
        $this->responseMock->expects($this->never())
            ->method('setPublicHeaders');
        $this->model->afterGenerateXml();
    }
}

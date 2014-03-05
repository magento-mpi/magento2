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
     * @var \Magento\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit_Framework_MockObject_MockObject
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
            ['isCacheable', 'getAllBlocks']
        );
        $this->responseMock = $this->getMock('\Magento\App\Response\Http', [], [], '', false);
        $this->configMock = $this->getMock('Magento\PageCache\Model\Config', [], [], '', false);

        $this->model = new \Magento\PageCache\Model\Layout\LayoutPlugin(
            $this->layoutMock,
            $this->responseMock,
            $this->configMock
        );
    }

    /**
     * @param $layoutIsCacheable
     * @dataProvider afterGenerateXmlDataProvider
     */
    public function testAfterGenerateXml($layoutIsCacheable)
    {
        $maxAge = 180;
        $result = 'test';

        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue($layoutIsCacheable));
        if ($layoutIsCacheable) {
            $this->configMock->expects($this->once())
                ->method('getTtl')
                ->will($this->returnValue($maxAge));
            $this->responseMock->expects($this->once())
                ->method('setPublicHeaders')
                ->with($maxAge);
        } else {
            $this->responseMock->expects($this->never())
                ->method('setPublicHeaders');
        }
        $output = $this->model->afterGenerateXml($this->layoutMock, $result);
        $this->assertSame($result, $output);

    }

    public function afterGenerateXmlDataProvider()
    {
        return [
            'Layout is cache-able' => [true],
            'Layout is not cache-able' => [false]
        ];
    }

    /**
     * @param bool $layoutIsCacheable
     * @param string $expectedTags
     * @param string $configCacheType
     * @param int $ttl
     * @dataProvider afterGetOutputDataProvider
     */
    public function testAfterGetOutput($layoutIsCacheable, $expectedTags, $configCacheType, $ttl)
    {
        $html = 'html';
        $blockStub = $this->getMock('Magento\PageCache\Block\Controller\StubBlock', null, array(), '', false);
        $blockStub->setTtl($ttl);
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue($layoutIsCacheable));
        $this->layoutMock->expects($this->any())
            ->method('getAllBlocks')
            ->will($this->returnValue(array($blockStub)));

        $this->configMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($configCacheType));

        if ($layoutIsCacheable) {
            $this->responseMock->expects($this->once())
                ->method('setHeader')
                ->with('X-Magento-Tags', $expectedTags);
        } else {
            $this->responseMock->expects($this->never())
                ->method('setHeader');
        }
        $output = $this->model->afterGetOutput($this->layoutMock, $html);
        $this->assertSame($output, $html);
    }

    public function afterGetOutputDataProvider()
    {
        $tags = 'identity1,identity2';
        return [
            'Cacheable layout' => [true, $tags, null, 0],
            'Non-cacheable layout' => [false, null, null, 0],
            'Cacheable layout with Varnish' => [true, $tags, \Magento\PageCache\Model\Config::VARNISH, 0],
            'Cacheable layout with Varnish and esi' => [true, null, \Magento\PageCache\Model\Config::VARNISH, 100],
            'Cacheable layout with Builtin' => [true, $tags, \Magento\PageCache\Model\Config::BUILT_IN, 0],
            'Cacheable layout with Builtin and esi' => [false, $tags, \Magento\PageCache\Model\Config::BUILT_IN, 100],
        ];
    }
} 
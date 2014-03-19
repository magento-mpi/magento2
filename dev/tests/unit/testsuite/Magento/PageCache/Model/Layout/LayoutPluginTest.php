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
            array(),
            '',
            false,
            true,
            true,
            array('isCacheable')
        );
        $this->responseMock = $this->getMock('\Magento\App\Response\Http', array(), array(), '', false);
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\App\ConfigInterface',
            array(),
            '',
            false,
            true,
            true,
            array('isSetFlag', 'getValue')
        );

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

        $this->layoutMock->expects($this->once())->method('isCacheable')->will($this->returnValue($layoutIsCacheable));
        if ($layoutIsCacheable) {
            $this->configMock->expects(
                $this->once()
            )->method(
                'getValue'
            )->with(
                \Magento\PageCache\Model\Config::XML_PAGECACHE_TTL
            )->will(
                $this->returnValue($maxAge)
            );
            $this->responseMock->expects($this->once())->method('setPublicHeaders')->with($maxAge);
        } else {
            $this->responseMock->expects($this->never())->method('setPublicHeaders');
        }
        $output = $this->model->afterGenerateXml($this->layoutMock, $result);
        $this->assertSame($result, $output);
    }

    public function afterGenerateXmlDataProvider()
    {
        return array('Layout is cache-able' => array(true), 'Layout is not cache-able' => array(false));
    }

    /**
     * @param bool $layoutIsCacheable
     * @dataProvider afterGetOutputDataProvider
     */
    public function testAfterGetOutput($layoutIsCacheable)
    {
        $html = 'html';

        $this->layoutMock->expects($this->once())->method('isCacheable')->will($this->returnValue($layoutIsCacheable));
        if ($layoutIsCacheable) {
            $this->responseMock->expects($this->once())->method('setHeader')->with('X-Magento-Tags');
        } else {
            $this->responseMock->expects($this->never())->method('setHeader');
        }
        $output = $this->model->afterGetOutput($this->layoutMock, $html);
        $this->assertSame($output, $html);
    }

    public function afterGetOutputDataProvider()
    {
        return array('Layout is cache-able' => array(true), 'Layout is not cache-able' => array(false));
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\App\FrontController;

class HeaderPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\App\FrontController\HeaderPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Core\Model\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->configMock = $this->getMock('Magento\Core\Model\ConfigInterface', array(), array(), '', false);
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->plugin = new HeaderPlugin($this->layoutMock, $this->configMock);
    }

    /**
     * data providers for response headers
     */
    public function headersCachableDataProvider()
    {
        return array(
            array(false, false, '10', 'no-store, no-cache, must-revalidate, max-age=0',
                gmdate('D, d M Y H:i:s T', strtotime('-10 seconds')), 'no-cache'),
            array(true, false, '20', 'public, max-age=20',
                gmdate('D, d M Y H:i:s T', strtotime('+20 seconds')), 'cache'),
            array(true, true, '30', 'private, max-age=30',
                gmdate('D, d M Y H:i:s T', strtotime('+30 seconds')), 'cache'),
        );
    }

    /**
     * test response headers after dispatch, without cache
     *
     * @dataProvider headersCachableDataProvider
     */
    public function testAfterDispatchCacheable($isCacheable, $isPrivate, $maxAge, $cacheControl, $expires, $pragma)
    {
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue($isCacheable));
        $this->layoutMock->expects($this->any())
            ->method('isPrivate')
            ->will($this->returnValue($isPrivate));
        $this->configMock->expects($this->any())
            ->method('getValue')
            ->with('system/headers/max-age')
            ->will($this->returnValue($maxAge));

        $this->responseMock->expects($this->at(0))
            ->method('setHeader')
            ->with('pragma', $pragma);

        $this->responseMock->expects($this->at(1))
            ->method('setHeader')
            ->with('cache-control', $cacheControl);

        $this->responseMock->expects($this->at(2))
            ->method('setHeader')
            ->with('expires', $expires);

        $this->plugin->afterDispatch($this->responseMock);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PageCache\Model\Observer */
    protected $_model;

    /** @var  \Magento\PageCache\Model\Config */
    protected $_configMock;

    /** @var  \Magento\App\PageCache\Cache */
    protected $_cacheMock;

    /** @var \Magento\View\Element\AbstractBlock */
    protected $_blockMock;

    /** @var \Magento\Core\Model\Layout */
    protected $_layoutMock;

    /** @var \Magento\Event\Observer */
    protected $_observerMock;

    /** @var \Magento\PageCache\Helper\Data */
    protected $_helperMock;

    /** @var  \Magento\App\Cache\TypeListInterface */
    protected $_typeListMock;

    /** @var \Magento\Object */
    protected $_transport;

    /** @var  \Magento\App\Cache\StateInterface */
    protected $_cacheStateMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Object\ */
    protected $_observerObject;

    /**
     * Set up all mocks and data for test
     */
    public function setUp()
    {
        $this->_configMock = $this->getMock('Magento\PageCache\Model\Config', ['getType', 'isEnabled'], [], '', false);
        $this->_cacheMock = $this->getMock('Magento\App\PageCache\Cache', ['clean'], [], '', false);
        $this->_helperMock = $this->getMock('Magento\PageCache\Helper\Data', [], [], '', false);
        $this->_typeListMock  = $this->getMock('Magento\App\Cache\TypeList', [], [], '', false);
        $this->_cacheStateMock  = $this->getMock('Magento\App\Cache\State', [], [], '', false);

        $this->_model = new \Magento\PageCache\Model\Observer(
            $this->_configMock,
            $this->_cacheMock,
            $this->_helperMock,
            $this->_typeListMock,
            $this->_cacheStateMock
        );
        $this->_observerMock = $this->getMock('Magento\Event\Observer', ['getEvent'], [], '', false);
        $this->_layoutMock = $this->getMock(
            'Magento\Core\Model\Layout',
            ['isCacheable', 'getBlock', 'getUpdate', 'getHandles'],
            [],
            '',
            false
        );
        $this->_blockMock = $this->getMockForAbstractClass(
            'Magento\View\Element\AbstractBlock',
            [],
            '',
            false,
            true,
            true,
            ['getTtl', 'isScopePrivate', 'getNameInLayout', 'getUrl']
        );
        $this->_transport = new \Magento\Object([
            'output' => 'test output html'
        ]);
        $this->_observerObject = $this->getMock('\Magento\Core\Model\Store', [], [], '', false);
    }

    /**
     * @param bool $cacheState
     * @param bool $varnishIsEnabled
     * @param bool $scopeIsPrivate
     * @param int|null $blockTtl
     * @param string $expectedOutput
     * @dataProvider processLayoutRenderDataProvider
     */
    public function testProcessLayoutRenderElement($cacheState, $varnishIsEnabled, $scopeIsPrivate, $blockTtl, $expectedOutput)
    {
        $eventMock = $this->getMock('Magento\Event', ['getLayout', 'getElementName', 'getTransport'], [], '', false);
        $this->_observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $eventMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->_layoutMock));
        $this->_configMock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue($cacheState));

        if ($cacheState) {
            $eventMock->expects($this->once())
                ->method('getElementName')
                ->will($this->returnValue('blockName'));
            $eventMock->expects($this->once())
                ->method('getTransport')
                ->will($this->returnValue($this->_transport));
            $this->_layoutMock->expects($this->once())
                ->method('isCacheable')
                ->will($this->returnValue(true));

            $this->_layoutMock->expects($this->any())
                ->method('getUpdate')
                ->will($this->returnSelf());
            $this->_layoutMock->expects($this->any())
                ->method('getHandles')
                ->will($this->returnValue([]));
            $this->_layoutMock->expects($this->once())
                ->method('getBlock')
                ->will($this->returnValue($this->_blockMock));

            if ($varnishIsEnabled) {
                $this->_blockMock->setTtl($blockTtl);
                $this->_blockMock->expects($this->any())
                    ->method('getUrl')
                    ->will($this->returnValue('page_cache/block/wrapesi/with/handles/and/other/stuff'));
            }
            if ($scopeIsPrivate) {
                $this->_blockMock->expects($this->once())
                    ->method('getNameInLayout')
                    ->will($this->returnValue('testBlockName'));
                $this->_blockMock->expects($this->once())
                    ->method('isScopePrivate')
                    ->will($this->returnValue($scopeIsPrivate));
            }
            $this->_configMock->expects($this->any())
                ->method('getType')
                ->will($this->returnValue($varnishIsEnabled));
        }
        $this->_model->processLayoutRenderElement($this->_observerMock);

        $this->assertEquals($expectedOutput, $this->_transport['output']);
    }

    /**
     * Data provider for testProcessLayoutRenderElement
     *
     * @return array
     */
    public function processLayoutRenderDataProvider()
    {
        return [
            'full_page type and Varnish enabled, public scope, ttl is set' =>
                [true, true, false, 360, '<esi:include src="page_cache/block/wrapesi/with/handles/and/other/stuff" />'],
            'full_page type and Varnish enabled, public scope, ttl is not set' =>
                [true, true, false, null, 'test output html'],
            'full_page type enabled, Varnish disabled, public scope, ttl is set' =>
                [true, false, false, 360, 'test output html'],
            'full_page type enabled, Varnish disabled, public scope, ttl is not set' =>
                [true, false, false, null, 'test output html'],
            'full_page type enabled, Varnish disabled, private scope, ttl is not set' =>
                [true, false, true, null, '<!-- BLOCK testBlockName -->test output html<!-- /BLOCK testBlockName -->'],
            'full_page type is disabled, Varnish enabled' => [false, true, false, null, 'test output html']
        ];
    }

    /**
     * Test case for cache invalidation
     *
     * @dataProvider flushCacheByTagsDataProvider
     * @param $cacheState
     */
    public function testFlushCacheByTags($cacheState)
    {
        $this->_configMock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue($cacheState));

        if ($cacheState) {
            $tags = array('cache_1', 'cache_group');
            $expectedTags = array('cache_1', 'cache_group', 'cache');

            $eventMock = $this->getMock('Magento\Event', ['getObject'], [], '', false);
            $eventMock->expects($this->once())
                ->method('getObject')
                ->will($this->returnValue($this->_observerObject));
            $this->_observerMock->expects($this->once())
                ->method('getEvent')
                ->will($this->returnValue($eventMock));
            $this->_configMock->expects($this->once())
                ->method('getType')
                ->will($this->returnValue(0));
            $this->_observerObject->expects($this->once())
                ->method('getIdentities')
                ->will($this->returnValue($tags));

            $this->_cacheMock->expects($this->once())
                ->method('clean')
                ->with($this->equalTo($expectedTags));
        }

        $this->_model->flushCacheByTags($this->_observerMock);
    }

    public function flushCacheByTagsDataProvider()
    {
        return [
            'full_page cache type is enabled' => [true],
            'full_page cache type is disabled' => [false]
        ];
    }

    /**
     * Test case for flushing all the cache
     */
    public function testFlushAllCache()
    {
        $this->_configMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(0));

        $this->_cacheMock->expects($this->once())
            ->method('clean');
        $this->_model->flushAllCache($this->_observerMock);
    }
}

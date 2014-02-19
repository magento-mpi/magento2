<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PageCache\Model\Observer */
    protected $_model;

    /** @var  \Magento\Core\Model\Config */
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

    /** @var \Magento\Object */
    protected $_transport;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Object\ */
    protected $_observerObject;

    /**
     * Set up all mocks and data for test
     */
    public function setUp()
    {
        $this->_configMock = $this->getMock('Magento\PageCache\Model\Config', ['getType'], [], '', false);
        $this->_cacheMock = $this->getMock('Magento\App\PageCache\Cache', ['clean'], [], '', false);
        $this->_helperMock = $this->getMock('Magento\PageCache\Helper\Data', [], [], '', false);
        $this->_model = new \Magento\PageCache\Model\Observer(
            $this->_configMock,
            $this->_cacheMock,
            $this->_helperMock
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
     * @param bool $varnishIsEnabled
     * @param bool $scopeIsPrivate
     * @param int|null $blockTtl
     * @param string $expectedOutput
     * @dataProvider processLayoutRenderDataProvider
     */
    public function testProcessLayoutRenderElement($varnishIsEnabled, $scopeIsPrivate, $blockTtl, $expectedOutput)
    {
        $eventMock = $this->getMock('Magento\Event', ['getLayout', 'getElementName', 'getTransport'], [], '', false);
        $this->_observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $eventMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->_layoutMock));
        $eventMock->expects($this->once())
            ->method('getElementName')
            ->will($this->returnValue('blockName'));
        $eventMock->expects($this->once())
            ->method('getTransport')
            ->will($this->returnValue($this->_transport));
        $this->_layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));
        $this->_layoutMock->expects($this->once())
            ->method('getBlock')
            ->will($this->returnValue($this->_blockMock));
        $this->_layoutMock->expects($this->any())
            ->method('getUpdate')
            ->will($this->returnSelf());
        $this->_layoutMock->expects($this->any())
            ->method('getHandles')
            ->will($this->returnValue([]));

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
        $this->_configMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($varnishIsEnabled));

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
            'Varnish enabled, public scope, ttl is set' =>
                [true, false, 360, '<esi:include src="page_cache/block/wrapesi/with/handles/and/other/stuff" />'],
            'Varnish enabled, public scope, ttl is not set' => [true, false, null, 'test output html'],
            'Varnish disabled, public scope, ttl is set' => [false, false, 360, 'test output html'],
            'Varnish disabled, public scope, ttl is not set' => [false, false, null, 'test output html'],
            'Varnish disabled, private scope, ttl is not set' =>
                [false, true, null, '<!-- BLOCK testBlockName -->test output html<!-- /BLOCK testBlockName -->']
        ];
    }

    /**
     * Test case for cache invalidation
     */
    public function testInvalidateCache()
    {
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
        $tags = array('cache_1', 'cache_group');
        $this->_observerObject->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($tags));

        $this->_cacheMock->expects($this->once())
            ->method('clean')
            ->with($this->equalTo($tags));

        $this->_model->invalidateCache($this->_observerMock);
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

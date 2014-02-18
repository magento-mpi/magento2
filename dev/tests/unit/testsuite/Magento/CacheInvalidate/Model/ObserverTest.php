<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CacheInvalidate
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CacheInvalidate;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\CacheInvalidate\Model\Observer */
    protected $_model;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Event\Observer */
    protected $_observerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\HTTP\Adapter\Curl */
    protected $_curlMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\PageCache\Model\Config */
    protected $_configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\CacheInvalidate\Helper\Data */
    protected $_helperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Object\ */
    protected $_observerObject;

    /** @var string */
    protected $_url;

    /**
     * Set up all mocks and data for test
     */
    public function setUp()
    {
        $this->_url = 'http://mangento.index.php';
        $this->_configMock = $this->getMock('Magento\PageCache\Model\Config', ['getType'], [], '', false);
        $this->_helperMock = $this->getMock('Magento\PageCache\Helper\Data', ['getUrl'], [], '', false);
        $this->_curlMock = $this->getMock(
            '\Magento\HTTP\Adapter\Curl',
            ['setOptions', 'write', 'read', 'close'],
            [],
            '',
            false
        );
        $this->_model = new \Magento\CacheInvalidate\Model\Observer(
            $this->_configMock,
            $this->_helperMock,
            $this->_curlMock
        );
        $this->_observerMock = $this->getMock('Magento\Event\Observer', ['getEvent'], [], '', false);
        $this->_observerObject = $this->getMock('\Magento\Core\Model\Store',[], [], '', false);
        $this->_helperMock->expects($this->any())
            ->method('getUrl')
            ->with($this->equalTo('*'), array())
            ->will($this->returnValue('http://mangento.index.php'));
    }

    /**
     * Test case for cache invalidation
     */
    public function testInvalidateVarnish()
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
            ->will($this->returnValue(1));
        $tags = array('cache_1', 'cache_group');
        $this->_observerObject->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($tags));
        $this->sendPurgeRequest(implode('|', $tags));

        $this->_model->invalidateVarnish($this->_observerMock);
    }

    /**
     * Test case for flushing all the cache
     */
    public function testFlushAllCache()
    {
        $this->_configMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(1));

        $this->sendPurgeRequest('.*');
        $this->_model->flushAllCache($this->_observerMock);
    }

    /**
     * @param array $tags
     */
    protected function sendPurgeRequest($tags = array())
    {
        $httpVersion = '1.1';
        $headers = "X-Magento-Tags-Pattern: {$tags}";
        $this->_curlMock->expects($this->once())
            ->method('setOptions')
            ->with(array(CURLOPT_CUSTOMREQUEST => 'PURGE'));
        $this->_curlMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo(''), $this->equalTo($this->_url), $httpVersion, $headers);
        $this->_curlMock->expects($this->once())
            ->method('read');
        $this->_curlMock->expects($this->once())
            ->method('close');
    }
}
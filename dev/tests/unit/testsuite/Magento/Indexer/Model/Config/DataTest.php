<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\Config\Data
     */
    protected $model;

    /**
     * @var \Magento\Indexer\Model\Config\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

    /**
     * @var \Magento\Config\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * @var \Magento\Indexer\Model\Resource\Indexer\State\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateCollection;

    /**
     * @var string
     */
    protected $cacheId = 'indexer_config';

    /**
     * @var string
     */
    protected $indexers = array(
        'indexer1' => array(),
        'indexer3' => array(),
    );

    protected function setUp()
    {
        $this->reader = $this->getMock(
            'Magento\Indexer\Model\Config\Reader', array('read'), array(), '', false
        );
        $this->cache = $this->getMockForAbstractClass(
            'Magento\Config\CacheInterface', array(), '', false, false, true, array('test', 'load', 'save')
        );
        $this->stateCollection = $this->getMock(
            'Magento\Indexer\Model\Resource\Indexer\State\Collection', array('getItems'), array(), '', false
        );
    }

    public function testConstructorWithCache()
    {
        $this->cache->expects($this->once())
            ->method('test')
            ->with($this->cacheId)
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('load')
            ->with($this->cacheId)
            ->will($this->returnValue(serialize($this->indexers)));

        $this->stateCollection->expects($this->never())
            ->method('getItems');

        $this->model = new \Magento\Indexer\Model\Config\Data(
            $this->reader,
            $this->cache,
            $this->stateCollection,
            $this->cacheId
        );
    }

    public function testConstructorWithoutCache()
    {
        $this->cache->expects($this->once())
            ->method('test')
            ->with($this->cacheId)
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('load')
            ->with($this->cacheId)
            ->will($this->returnValue(false));

        $this->reader->expects($this->once())
            ->method('read')
            ->will($this->returnValue($this->indexers));

        $stateExistent = $this->getMock(
            'Magento\Indexer\Model\Indexer\State', array('getIndexerId', '__wakeup', 'delete'), array(), '', false
        );
        $stateExistent->expects($this->once())
            ->method('getIndexerId')
            ->will($this->returnValue('indexer1'));
        $stateExistent->expects($this->never())
            ->method('delete');

        $stateNonexistent = $this->getMock(
            'Magento\Indexer\Model\Indexer\State', array('getIndexerId', '__wakeup', 'delete'), array(), '', false
        );
        $stateNonexistent->expects($this->once())
            ->method('getIndexerId')
            ->will($this->returnValue('indexer2'));
        $stateNonexistent->expects($this->once())
            ->method('delete');

        $states = array(
            $stateExistent,
            $stateNonexistent,
        );

        $this->stateCollection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($states));

        $this->model = new \Magento\Indexer\Model\Config\Data(
            $this->reader,
            $this->cache,
            $this->stateCollection,
            $this->cacheId
        );
    }
}

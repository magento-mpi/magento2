<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\Config\Data
     */
    protected $model;

    /**
     * @var \Magento\Mview\Config\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

    /**
     * @var \Magento\Config\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * @var \Magento\Mview\View\State\CollectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateCollection;

    /**
     * @var string
     */
    protected $cacheId = 'mview_config';

    /**
     * @var string
     */
    protected $views = array(
        'view1' => array(),
        'view3' => array(),
    );

    protected function setUp()
    {
        $this->reader = $this->getMock(
            'Magento\Mview\Config\Reader', array('read'), array(), '', false
        );
        $this->cache = $this->getMockForAbstractClass(
            'Magento\Config\CacheInterface', array(), '', false, false, true, array('test', 'load', 'save')
        );
        $this->stateCollection = $this->getMockForAbstractClass(
            'Magento\Mview\View\State\CollectionInterface',
            array(), '', false, false, true, array('getItems')
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
            ->will($this->returnValue(serialize($this->views)));

        $this->stateCollection->expects($this->never())
            ->method('getItems');

        $this->model = new \Magento\Mview\Config\Data(
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
            ->will($this->returnValue($this->views));

        $stateExistent = $this->getMock(
            'Magento\Mview\Indexer\State', array('getViewId', '__wakeup', 'delete'), array(), '', false
        );
        $stateExistent->expects($this->once())
            ->method('getViewId')
            ->will($this->returnValue('view1'));
        $stateExistent->expects($this->never())
            ->method('delete');

        $stateNonexistent = $this->getMock(
            'Magento\Mview\Indexer\State', array('getViewId', '__wakeup', 'delete'), array(), '', false
        );
        $stateNonexistent->expects($this->once())
            ->method('getViewId')
            ->will($this->returnValue('view2'));
        $stateNonexistent->expects($this->once())
            ->method('delete');

        $states = array(
            $stateExistent,
            $stateNonexistent,
        );

        $this->stateCollection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($states));

        $this->model = new \Magento\Mview\Config\Data(
            $this->reader,
            $this->cache,
            $this->stateCollection,
            $this->cacheId
        );
    }
}

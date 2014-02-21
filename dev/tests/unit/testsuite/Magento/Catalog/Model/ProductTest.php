<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryIndexerMock;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_processor;

    public function setUp()
    {
        $this->categoryIndexerMock = $this->getMockForAbstractClass(
            '\Magento\Indexer\Model\IndexerInterface', array(), '', false, false, true, array()
        );

        $this->_processor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor', array(), array(), '', false
        );

        $stateMock = $this->getMock(
            'Magento\App\State',
            array('getAreaCode'), array(), '', false
        );

        $stateMock->expects($this->any())
            ->method('getAreaCode')
            ->will($this->returnValue(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE));

        $eventManagerMock = $this->getMock(
            'Magento\Event\ManagerInterface',
            array(), array(), '', false
        );

        $cacheInterfaceMock = $this->getMock(
            'Magento\App\CacheInterface',
            array(), array(), '', false
        );


        $contextMock = $this->getMock(
            '\Magento\Core\Model\Context',
            array('getEventDispatcher', 'getCacheManager', 'getAppState'), array(), '', false
        );

        $contextMock->expects($this->any())
            ->method('getAppState')
            ->will($this->returnValue($stateMock));

        $contextMock->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($eventManagerMock));

        $contextMock->expects($this->any())
            ->method('getCacheManager')
            ->will($this->returnValue($cacheInterfaceMock));

        $this->_model = new \Magento\Catalog\Model\Product(
            $contextMock,
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Url', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Link', array(), array(), '', false),
            $this->getMock(
                'Magento\Catalog\Model\Product\Configuration\Item\OptionFactory',
                array(), array(), '', false
            ),
            $this->getMock('Magento\CatalogInventory\Model\Stock\ItemFactory', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\CategoryFactory', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Option', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Visibility', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Attribute\Source\Status', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Media\Config', array(), array(), '', false),
            $this->getMock('Magento\Index\Model\Indexer', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\Type', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Helper\Image', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Helper\Product', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Resource\Product', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '', false),
            $this->getMock('Magento\Data\CollectionFactory', array(), array(), '', false),
            $this->getMock('Magento\App\Filesystem', array(), array(), '', false),
            $this->categoryIndexerMock,
            $this->_processor,
            array('id' => 1)
        );
    }

    public function testIndexerAfterDeleteCommitProduct()
    {
        $this->categoryIndexerMock->expects($this->once())
            ->method('reindexRow');
        $this->_processor->expects($this->once())
            ->method('reindexRow');

        $this->_model->delete();
    }

    public function testReindex()
    {
        $this->categoryIndexerMock->expects($this->once())
            ->method('reindexRow');
        $this->_processor->expects($this->once())
            ->method('reindexRow');

        $this->_model->reindex();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GroupedProduct
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\Type;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogProductLink;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectHelper;

    protected function setUp()
    {
        $this->objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $coreDataMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $fileStorageDbMock = $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false);
        $this->catalogProductLink = $this->getMock('Magento\Catalog\Model\Resource\Product\Link',
            array(), array(), '', false);
        $this->_model = $this->objectHelper->getObject('Magento\GroupedProduct\Model\Product\Type\Grouped', array(
            'eventManager' => $eventManager,
            'coreData' => $coreDataMock,
            'fileStorageDb' => $fileStorageDbMock,
            'filesystem' => $filesystem,
            'coreRegistry' => $coreRegistry,
            'logger' => $logger,
            'productFactory' => $productFactoryMock,
            'catalogProductLink' => $this->catalogProductLink,
        ));
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }

    public function testGetChildrenIds()
    {
        $childrenIds = array(100, 200, 300);
        $this->catalogProductLink->expects($this->once())->method('getChildrenIds')
            ->will($this->returnValue($childrenIds));
        $this->assertEquals($childrenIds, $this->_model->getChildrenIds(12345));
    }

    public function testGetParentIdsByChild()
    {
        $parentIds = array(100, 200, 300);
        $this->catalogProductLink->expects($this->once())->method('getParentIdsByChild')
            ->will($this->returnValue($parentIds));
        $this->assertEquals($parentIds, $this->_model->getParentIdsByChild(12345));
    }

    public function testGetAssociatedProducts()
    {
        $cached = true; //non-cached case is rather difficult to cover
        $associatedProducts = array(5, 7, 11, 13, 17);
        $this->product->expects($this->once())->method('hasData')->will($this->returnValue($cached));
        $this->product->expects($this->once())->method('getData')->will($this->returnValue($associatedProducts));
        $this->assertEquals($associatedProducts, $this->_model->getAssociatedProducts($this->product));
    }

    /**
     * @param $status int
     * @param $filters array
     * @param $result array
     * @dataProvider addStatusProvider
     */
    public function testAddStatusFilter($status, $filters, $result)
    {
        $this->product->expects($this->once())->method('getData')->will($this->returnValue($filters));
        $this->product->expects($this->once())->method('setData')
            ->with('_cache_instance_status_filters', $result);
        $this->_model->addStatusFilter($status, $this->product);
    }

    public function addStatusProvider()
    {
        return array(
            array(1, array(), array(1)),
            array(1, false, array(1)),
        );
    }

    public function testGetStatusFiltersNoData()
    {
        $result = array(1, 2);
        $this->product->expects($this->once())->method('hasData')->will($this->returnValue(false));
        $this->assertEquals($result, $this->_model->getStatusFilters($this->product));
    }

    public function testGetStatusFiltersWithData()
    {
        $result = array(1, 2);
        $this->product->expects($this->once())->method('hasData')->will($this->returnValue(true));
        $this->product->expects($this->once())->method('getData')->will($this->returnValue($result));
        $this->assertEquals($result, $this->_model->getStatusFilters($this->product));
    }

    public function testGetAssociatedProductCollection()
    {
        $link = $this->getMock('Magento\Catalog\Model\Product\Link', array(), array(), '', false);
        $this->product->expects($this->once())->method('getLinkInstance')->will($this->returnValue($link));
        $link->expects($this->once())->method('useGroupedLinks')->will($this->returnValue($link));
        $collection = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Link\Product\Collection',
            array('setFlag', 'setIsStrongMode', 'setProduct'),
            array(),
            '',
            false
        );
        $link->expects($this->once())->method('getProductCollection')->will($this->returnValue($collection));
        $collection->expects($this->any())->method('setFlag')->will($this->returnValue($collection));
        $collection->expects($this->once())->method('setIsStrongMode')
                ->will($this->returnValue($collection));
        $this->assertEquals($collection, $this->_model->getAssociatedProductCollection($this->product));
    }

    public function testSave()
    {
        $link = $this->getMock('Magento\Catalog\Model\Product\Link', array(), array(), '', false);
        $this->product->expects($this->once())->method('getLinkInstance')->will($this->returnValue($link));
        $this->assertEquals($this->_model, $this->_model->save($this->product));
    }

    public function testProcessBuyRequest()
    {
        $basic = array(1, 2, 3);
        $result = array('super_group' => $basic);
        $buyRequest = $this->getMock('\Magento\Object', array('getSuperGroup'), array(), '', false);
        $buyRequest->expects($this->any())->method('getSuperGroup')->will($this->returnValue($basic));

        $this->assertEquals($result, $this->_model->processBuyRequest($this->product, $buyRequest));
    }
}

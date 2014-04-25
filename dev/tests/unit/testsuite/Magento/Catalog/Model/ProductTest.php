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

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $model;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryIndexerMock;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFlatProcessor;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productPriceProcessor;

    /**
     * @var Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeMock;

    /**
     * @var \Magento\Framework\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceInfoMock;

    public function setUp()
    {
        $this->categoryIndexerMock = $this->getMockForAbstractClass(
            '\Magento\Indexer\Model\IndexerInterface',
            array(),
            '',
            false,
            false
        );

        $this->productFlatProcessor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor',
            array(),
            array(),
            '',
            false
        );

        $this->_priceInfoMock = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);
        $this->productTypeMock = $this->getMock('Magento\Catalog\Model\Product\Type', [], [], '', false);
        $this->productPriceProcessor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor',
            array(),
            array(),
            '',
            false
        );

        $stateMock = $this->getMock('Magento\FrameworkApp\State', array('getAreaCode'), array(), '', false);
        $stateMock->expects($this->any())
            ->method('getAreaCode')
            ->will($this->returnValue(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE));

        $eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false);
        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction', 
            [], 
            [], 
            '', 
            false
        );
        $actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $cacheInterfaceMock = $this->getMock('Magento\Framework\App\CacheInterface', array(), array(), '', false);

        $contextMock = $this->getMock(
            '\Magento\Framework\Model\Context',
            array('getEventDispatcher', 'getCacheManager', 'getAppState', 'getActionValidator'), array(), '', false
        );
        $contextMock->expects($this->any())->method('getAppState')->will($this->returnValue($stateMock));
        $contextMock->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($eventManagerMock));
        $contextMock->expects($this->any())
            ->method('getCacheManager')
            ->will($this->returnValue($cacheInterfaceMock));
        $contextMock->expects($this->any())
            ->method('getActionValidator')
            ->will($this->returnValue($actionValidatorMock));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product',
            [
                'context' => $contextMock,
                'catalogProductType' => $this->productTypeMock,
                'categoryIndexer' => $this->categoryIndexerMock,
                'productFlatIndexerProcessor' => $this->productFlatProcessor,
                'productPriceIndexerProcessor' => $this->productPriceProcessor,
                'data' => array('id' => 1)
            ]
        );
    }

    public function testIndexerAfterDeleteCommitProduct()
    {
        $this->categoryIndexerMock->expects($this->once())->method('reindexRow');
        $this->productFlatProcessor->expects($this->once())->method('reindexRow');
        $this->productPriceProcessor->expects($this->once())->method('reindexRow');
        $this->assertSame($this->model, $this->model->delete());
    }

    public function testReindex()
    {
        $this->categoryIndexerMock->expects($this->once())->method('reindexRow');
        $this->productFlatProcessor->expects($this->once())->method('reindexRow');
        $this->assertNull($this->model->reindex());
    }

    public function testPriceReindexCallback()
    {
        $this->productPriceProcessor->expects($this->once())->method('reindexRow');
        $this->assertNull($this->model->priceReindexCallback());
    }

    /**
     * @dataProvider getIdentitiesProvider
     * @param array $expected
     * @param array $origData
     * @param array $data
     * @param bool $isDeleted
     */
    public function testGetIdentities($expected, $origData, $data, $isDeleted = false)
    {
        $this->model->setIdFieldName('id');
        $typeMock = $this->getMock('Magento\Catalog\Model\Product\Type\AbstractType', array(), array(), '', false);

        $this->productTypeMock
            ->expects($this->once())
            ->method('factory')
            ->with($this->model)
            ->will($this->returnValue($typeMock));

        $typeMock->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue(array('type_1')));
        if (is_array($origData)) {
            foreach ($origData as $key => $value) {
                $this->model->setOrigData($key, $value);
            }
        }
        $this->model->setData($data);
        $this->model->isDeleted($isDeleted);
        $this->assertEquals($expected, $this->model->getIdentities());
    }

    /**
     * @return array
     */
    public function getIdentitiesProvider()
    {
        return array(
            array(
                array('catalog_product_1', 'type_1', 'catalog_category_product_1'),
                array('id' => 1, 'name' => 'value', 'category_ids' => array(1)),
                array('id' => 1, 'name' => 'value', 'category_ids' => array(1))
            ),
            array(
                array('catalog_product_1', 'type_1', 'catalog_category_1'),
                null,
                array('id' => 1, 'name' => 'value', 'category_ids' => array(1))
            ),
            array(
                array('catalog_product_1', 'type_1', 'catalog_category_1'),
                array('id' => 1, 'name' => '', 'category_ids' => array(1)),
                array('id' => 1, 'name' => 'value', 'category_ids' => array(1))
            ),
            array(
                array('catalog_product_1', 'type_1', 'catalog_category_1'),
                array('id' => 1, 'name' => 'value', 'category_ids' => array(1)),
                array('id' => 1, 'name' => 'value', 'category_ids' => array(1)),
                true
            ),
        );
    }

    /**
     * Test retrieving price Info
     */
    public function testGetPriceInfo()
    {
        $this->productTypeMock->expects($this->once())
            ->method('getPriceInfo')
            ->with($this->equalTo($this->model))
            ->will($this->returnValue($this->_priceInfoMock));
        $this->assertEquals($this->model->getPriceInfo(), $this->_priceInfoMock);
    }

    /**
     * Test for set qty
     */
    public function testSetQty()
    {
        $this->productTypeMock->expects($this->once())
            ->method('getPriceInfo')
            ->with($this->equalTo($this->model))
            ->will($this->returnValue($this->_priceInfoMock));
        $this->assertEquals($this->model, $this->model->setQty(1));
        $this->assertEquals($this->model->getPriceInfo(), $this->_priceInfoMock);
    }

    /**
     * Test reload PriceInfo
     */
    public function testReloadPriceInfo()
    {
        $this->productTypeMock->expects($this->exactly(2))
            ->method('getPriceInfo')
            ->with($this->equalTo($this->model))
            ->will($this->returnValue($this->_priceInfoMock));
        $this->assertEquals($this->_priceInfoMock, $this->model->getPriceInfo());
        $this->assertEquals($this->_priceInfoMock, $this->model->reloadPriceInfo());
    }
}

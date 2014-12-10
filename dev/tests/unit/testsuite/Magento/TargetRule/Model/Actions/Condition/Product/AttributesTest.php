<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Actions\Condition\Product;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TargetRule\Model\Actions\Condition\Product\Attributes */
    protected $attributes;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $backendHelperMock;

    /** @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $configMock;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var \Magento\Catalog\Model\Resource\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $resourceProduct;

    /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $collectionMock;

    /** @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $formatInterfaceMock;

    /** @var \Magento\Rule\Block\Editable|\PHPUnit_Framework_MockObject_MockObject */
    protected $editableMock;

    /** @var \Magento\Catalog\Model\Product\Type|\PHPUnit_Framework_MockObject_MockObject */
    protected $typeMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Rule\Model\Condition\Context', [], [], '', false);
        $this->backendHelperMock = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->configMock = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->collectionMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection',
            [],
            [],
            '',
            false
        );
        $this->formatInterfaceMock = $this->getMock('\Magento\Framework\Locale\FormatInterface');
        $this->editableMock = $this->getMock('Magento\Rule\Block\Editable', [], [], '', false);
        $this->typeMock = $this->getMock('Magento\Catalog\Model\Product\Type', [], [], '', false);
        $this->resourceProduct = $this->getMock('Magento\Catalog\Model\Resource\Product', [], [], '', false);
        $this->resourceProduct->expects($this->any())->method('loadAllAttributes')->will($this->returnSelf());
        $this->resourceProduct->expects($this->any())->method('getAttributesByCode')->will($this->returnSelf());

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->attributes = $this->objectManagerHelper->getObject(
            'Magento\TargetRule\Model\Actions\Condition\Product\Attributes', [
                'context' => $this->contextMock,
                'backendData' => $this->backendHelperMock,
                'config' => $this->configMock,
                'product' => $this->productMock,
                'productResource' => $this->resourceProduct,
                'attrSetCollection' => $this->collectionMock,
                'localeFormat' => $this->formatInterfaceMock,
                'editable' => $this->editableMock,
                'type' => $this->typeMock
            ]
        );
    }

    /**
     * @dataProvider getConditionForCollectionDataProvider
     *
     * @param string $operator
     * @param string $whereCondition
     */
    public function testGetConditionForCollection($operator, $whereCondition)
    {
        $this->attributes->setAttribute('category_ids');
        $this->attributes->setValueType('constant');
        $this->attributes->setValue(3);
        $this->attributes->setOperator($operator);

        $collection = null;
        $resource = $this->getMock('Magento\TargetRule\Model\Resource\Index', [], [], '', false);
        $resource->expects($this->any())->method('getTable')->will($this->returnArgument(0));
        $resource->expects($this->any())->method('bindArrayOfIds')->with(3)->will($this->returnValue([3]));
        $resource->expects($this->any())->method('getOperatorCondition')
            ->with('category_id', $operator, [3])
            ->will($this->returnValue($whereCondition));

        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $select->expects($this->any())->method('from')->with('catalog_category_product', 'COUNT(*)')
            ->will($this->returnSelf());
        $select->expects($this->at(1))->method('where')->with('product_id=e.entity_id')->will($this->returnSelf());
        $select->expects($this->at(2))->method('where')->with($whereCondition)->will($this->returnSelf());
        $select->expects($this->any())->method('assemble')->will($this->returnValue('assembled select'));

        $object = $this->getMock('Magento\TargetRule\Model\Index', [], [], '', false);
        $object->expects($this->any())->method('getResource')->will($this->returnValue($resource));
        $object->expects($this->any())->method('select')->will($this->returnValue($select));
        $bind = [];
        $result = $this->attributes->getConditionForCollection($collection, $object, $bind);
        $this->assertEquals(
            '(assembled select) > 0',
            (string)$result
        );
    }

    /**
     * @return array
     */
    public function getConditionForCollectionDataProvider()
    {
        return [
            ['==', "`category_id`='3'"],
            ['()', "`category_id` IN ('3')"],
        ];
    }
}

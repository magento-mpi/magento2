<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition\Product;

use ReflectionMethod;
use ReflectionProperty;

class AbstractProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_condition;

    public function setUp()
    {
        $this->_condition = $this->getMockForAbstractClass(
            '\Magento\Rule\Model\Condition\Product\AbstractProduct',
            [],
            '',
            false
        );
    }

    public function testGetjointTables()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals([], $this->_condition->getTablesToJoin());
    }

    public function testGetMappedSqlField()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals('e.entity_id', $this->_condition->getMappedSqlField());
    }

    /**
     * @param array $setData
     * @param string $attributeObjectFrontendInput
     * @param array $attrObjectSourceAllOptionsValue
     * @param array $attrSetCollectionOptionsArray
     * @param bool $expectedAttrObjSourceAllOptionsParam
     * @param array $expectedValueSelectOptions
     * @param array $expectedValueOption
     * @dataProvider prepareValueOptionsDataProvider
     */
    public function testPrepareValueOptions($setData,
                                            $attributeObjectFrontendInput,
                                            $attrObjectSourceAllOptionsValue,
                                            $attrSetCollectionOptionsArray,
                                            $expectedAttrObjSourceAllOptionsParam,
                                            $expectedValueSelectOptions,
                                            $expectedValueOption)
    {
        foreach ($setData as $key => $value) {
            $this->_condition->setData($key, $value);
        }

        $attrObjectSourceMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\Source\AbstractSource')
            ->setMethods(['getAllOptions'])
            ->disableOriginalConstructor()
            ->getMock();
        $attrObjectSourceMock
            ->expects(is_null($expectedAttrObjSourceAllOptionsParam) ? $this->never() : $this->once())
            ->method('getAllOptions')
            ->with($expectedAttrObjSourceAllOptionsParam)
            ->willReturn($attrObjectSourceAllOptionsValue);

        $attributeObjectMock = $this->getMockBuilder('Magento\Catalog\Model\Resource\Eav\Attribute')
            ->setMethods(['usesSource', 'getFrontendInput', 'getSource', 'getAllOptions'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeObjectMock->method('usesSource')->willReturn(true);
        $attributeObjectMock
            ->expects(is_null($attributeObjectFrontendInput) ? $this->never() : $this->once())
            ->method('getFrontendInput')
            ->willReturn($attributeObjectFrontendInput);
        $attributeObjectMock->method('getSource')->willReturn($attrObjectSourceMock);

        $entityTypeMock = $this->getMockBuilder('Magento\Framework\Model\AbstractModel\Type')
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $entityTypeMock->method('getId')->willReturn('SomeEntityType');

        $configValueMock = $this->getMock('Magento\Eav\Model\Config',  ['getAttribute', 'getEntityType'], [], '', false);
        $configValueMock->method('getAttribute')->willReturn($attributeObjectMock);
        $configValueMock->method('getEntityType')->willReturn($entityTypeMock);

        $configProperty = new ReflectionProperty('Magento\Rule\Model\Condition\Product\AbstractProduct', '_config');
        $configProperty->setAccessible(true);
        $configProperty->setValue($this->_condition, $configValueMock);

        $attrSetCollectionValueMock = $this->getMockBuilder('Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection')
            ->setMethods(['setEntityTypeFilter', 'load', 'toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $attrSetCollectionValueMock->method('setEntityTypeFilter')->will($this->returnSelf());
        $attrSetCollectionValueMock->method('load')->will($this->returnSelf());
        $attrSetCollectionValueMock
            ->expects(is_null($attrSetCollectionOptionsArray) ? $this->never() : $this->once())
            ->method('toOptionArray')
            ->willReturn($attrSetCollectionOptionsArray);

        $attrSetCollectionProperty =
            new ReflectionProperty('Magento\Rule\Model\Condition\Product\AbstractProduct', '_attrSetCollection');
        $attrSetCollectionProperty->setAccessible(true);
        $attrSetCollectionProperty->setValue($this->_condition, $attrSetCollectionValueMock);

        $testedMethod = new ReflectionMethod('Magento\Rule\Model\Condition\Product\AbstractProduct', '_prepareValueOptions');
        $testedMethod->setAccessible(true);
        $testedMethod->invoke($this->_condition);

        $this->assertEquals($expectedValueSelectOptions, $this->_condition->getData('value_select_options'));
        $this->assertEquals($expectedValueOption, $this->_condition->getData('value_option'));
    }

    /**
     * @return array
     */
    public function prepareValueOptionsDataProvider()
    {
        return [
            [
                [
                    'value_select_options' => ['key' => 'value'],
                    'value_option' => ['k' => 'v']
                ], null, null, null, null, ['key' => 'value'], ['k' => 'v']
            ],

            [
                ['attribute' => 'attribute_set_id'],
                null,
                null,
                [
                    ['value' => 'value1', 'label' => 'Label for value 1'],
                    ['value' => 'value2', 'label' => 'Label for value 2'],
                ],
                null,
                [
                    ['value' => 'value1', 'label' => 'Label for value 1'],
                    ['value' => 'value2', 'label' => 'Label for value 2'],
                ],
                [
                    'value1' => 'Label for value 1',
                    'value2' => 'Label for value 2'
                ]
            ],

            [
                [
                    'value_select_options' => [
                        ['value' => 'value3', 'label' => 'Label for value 3'],
                        ['value' => 'value4', 'label' => 'Label for value 4'],
                    ],
                    'attribute' => 'type_id'
                ],
                null,
                null,
                null,
                null,
                [
                    ['value' => 'value3', 'label' => 'Label for value 3'],
                    ['value' => 'value4', 'label' => 'Label for value 4'],
                ],
                [
                    'value3' => 'Label for value 3',
                    'value4' => 'Label for value 4'
                ]
            ],

            [
                [
                    'value_select_options' => [
                        'value5' => 'Label for value 5',
                        'value6' => 'Label for value 6',
                    ],
                    'attribute' => 'type_id'
                ],
                null,
                null,
                null,
                null,
                [
                    ['value' => 'value5', 'label' => 'Label for value 5'],
                    ['value' => 'value6', 'label' => 'Label for value 6'],
                ],
                [
                    'value5' => 'Label for value 5',
                    'value6' => 'Label for value 6'
                ]
            ],

            [
                [],
                'multiselect',
                [
                    ['value' => 'value7', 'label' => 'Label for value 7'],
                    ['value' => 'value8', 'label' => 'Label for value 8'],
                ],
                null,
                false,
                [
                    ['value' => 'value7', 'label' => 'Label for value 7'],
                    ['value' => 'value8', 'label' => 'Label for value 8'],
                ],
                [
                    'value7' => 'Label for value 7',
                    'value8' => 'Label for value 8',
                ],
            ],

            [
                [],
                'multiselect',
                [
                    ['value' => 'valueA', 'label' => 'Label for value A'],
                    ['value' => ['array value'], 'label' => 'Label for value B'],
                ],
                null,
                false,
                [
                    ['value' => 'valueA', 'label' => 'Label for value A'],
                    ['value' => ['array value'], 'label' => 'Label for value B'],
                ],
                [
                    'valueA' => 'Label for value A',
                ],
            ],

            [
                [],
                'select',
                [
                    ['value' => 'value7', 'label' => 'Label for value 7'],
                    ['value' => 'value8', 'label' => 'Label for value 8'],
                    ['value' => 'default', 'label' => 'Default Option']
                ],
                null,
                true,
                [
                    ['value' => 'value7', 'label' => 'Label for value 7'],
                    ['value' => 'value8', 'label' => 'Label for value 8'],
                    ['value' => 'default', 'label' => 'Default Option']
                ],
                [
                    'value7' => 'Label for value 7',
                    'value8' => 'Label for value 8',
                    'default' => 'Default Option'
                ],
            ]
        ];
    }
}

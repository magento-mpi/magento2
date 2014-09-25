<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use Magento\TestFramework\Matcher\MethodInvokedAtIndex;

class ItemMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Service\V1\Data\ItemMapper */
    protected $itemMapper;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Rma\Service\V1\Data\ItemBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $itemBuilderMock;

    protected function setUp()
    {
        $this->itemBuilderMock = $this->getMock('Magento\Rma\Service\V1\Data\ItemBuilder', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->itemMapper = $this->objectManagerHelper->getObject(
            'Magento\Rma\Service\V1\Data\ItemMapper',
            [
                'itemBuilder' => $this->itemBuilderMock
            ]
        );
    }

    public function testExtractDto()
    {
        $data = ['itemValue' => 'itemData'];
        $itemMock = $this->getMockBuilder('Magento\Rma\Model\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $itemDto = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        list($attributes, $expectedAttributes) = $this->getAttributes($itemMock);

        $itemMock->expects($this->once())->method('getAttributes')
            ->will($this->returnValue($attributes));
        $itemMock->expects(new MethodInvokedAtIndex(count($attributes)))->method('getData')
            ->with()
            ->will($this->returnValue($data));
        $this->itemBuilderMock->expects($this->once())->method('populateWithArray')
            ->with(array_merge($expectedAttributes, $data));
        $this->itemBuilderMock->expects($this->once())->method('create')
            ->will($this->returnValue($itemDto));

        $this->assertSame($itemDto, $this->itemMapper->extractDto($itemMock));
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $item
     * @return array
     */
    private function getAttributes(\PHPUnit_Framework_MockObject_MockObject $item)
    {
        $attributesData = [
            ['null' => null],
            ['entity_id' => '123123'],
            ['ordinary' => 'ordinary data']
        ];
        $expectedAttributes = [
            Item::ID => $attributesData[1]['entity_id'],
            'ordinary' => $attributesData[2]['ordinary']
        ];

        $attributes = [];
        foreach ($attributesData as $index => $data) {
            $attribute = $this->getMockBuilder('Magento\Rma\Model\Item\Attribute')
                ->disableOriginalConstructor()
                ->setMethods(['getAttributeCode', '__wakeup'])
                ->getMock();
            $attribute->expects($this->once())->method('getAttributeCode')
                ->will($this->returnValue(key($data)));

            $item->expects(new MethodInvokedAtIndex($index))->method('getData')
                ->with(key($data))
                ->will($this->returnValue(current($data)));

            $attributes[] = $attribute;
        }
        $item->expects($this->exactly(count($attributesData)))->method('getDataUsingMethod')
            ->with(
                $this->callback(
                    function ($value) {
                        return in_array($value, ['null', 'entity_id', 'ordinary']);
                    }
                )
            )->will($this->returnValue(null));

        return [$attributes, $expectedAttributes];
    }
}

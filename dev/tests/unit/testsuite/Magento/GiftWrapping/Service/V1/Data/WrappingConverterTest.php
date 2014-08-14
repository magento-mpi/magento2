<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class WrappingConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Service\V1\Data\WrappingConverter */
    protected $wrappingConverter;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingFactoryMock;

    protected function setUp()
    {
        $this->wrappingFactoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\WrappingFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wrappingConverter = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Service\V1\Data\WrappingConverter',
            [
                'wrappingFactory' => $this->wrappingFactoryMock
            ]
        );
    }

    public function testGetModel()
    {
        $id = 1;
        $data = ['field' => 'data'];
        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        /** @var Wrapping\Image|\PHPUnit_Framework_MockObject_MockObject $imageDto */
        $imageDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping\Image')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->wrappingFactoryMock->expects($this->once())->method('create')->will($this->returnValue($wrappingModel));
        $wrappingDto->expects($this->exactly(2))->method('getWrappingId')->will($this->returnValue($id));
        $wrappingModel->expects($this->once())->method('load')->with($id);
        $wrappingDto->expects($this->once())->method('__toArray')->will($this->returnValue($data));
        $wrappingModel->expects($this->once())->method('addData')->with($data);

        $wrappingDto->expects($this->once())->method('getImage')->will($this->returnValue($imageDto));
        /** @todo add image expectations */

        $this->assertSame($wrappingModel, $this->wrappingConverter->getModel($wrappingDto));
    }
}

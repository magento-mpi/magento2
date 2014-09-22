<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Bundle\Service\V1\Data\Product\Option\Type;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\Type\ReadService
     */
    private $model;

    /**
     * @var \Magento\Bundle\Model\Source\Option\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    private $typeModel;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Option\TypeConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $typeConverter;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Option\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    private $type;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->typeModel = $this->getMockBuilder('Magento\Bundle\Model\Source\Option\Type')
            ->setMethods(['toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->typeConverter = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Option\TypeConverter')
            ->setMethods(['createDataFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->type = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Option\Type')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\Bundle\Service\V1\Product\Option\Type\ReadService',
            ['type' => $this->typeModel, 'typeConverter' => $this->typeConverter]
        );
    }

    public function testGetTypes()
    {
        $label = 'someLabel';
        $value = 'someValue';
        $this->typeModel->expects($this->once())->method('toOptionArray')
            ->will($this->returnValue([['label' => $label, 'value' => $value]]));

        $this->typeConverter->expects($this->once())->method('createDataFromModel')
            ->will($this->returnValue($this->type));

        $this->assertEquals([$this->type], $this->model->getTypes());
    }
}

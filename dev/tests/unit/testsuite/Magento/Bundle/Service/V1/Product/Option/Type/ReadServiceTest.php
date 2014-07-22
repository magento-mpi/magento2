<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Bundle\Service\V1\Data\Option\Type\Metadata;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\Type\ReadService
     */
    private $model;

    /**
     * @var \Magento\Bundle\Model\Source\Option\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionType;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Option\Type\MetadataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataBuilder;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->optionType = $this->getMockBuilder('Magento\Bundle\Model\Source\Option\Type')
            ->setMethods(['toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataBuilder = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Option\Type\MetadataBuilder')
            ->setMethods(['populateWithArray', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\Bundle\Service\V1\Product\Option\Type\ReadService',
            ['type' => $this->optionType, 'metadataBuilder' => $this->metadataBuilder]
        );
    }

    public function testGetTypes()
    {
        $metadata = 'OptionTypeMetadata';
        $label = 'someLabel';
        $value = 'someValue';
        $this->optionType->expects($this->once())->method('toOptionArray')
            ->will($this->returnValue([['label' => $label, 'value' => $value]]));

        $this->metadataBuilder->expects($this->once())->method('populateWithArray')
            ->with($this->equalTo([Metadata::LABEL => $label, Metadata::CODE => $value]))
            ->will($this->returnValue($this->metadataBuilder));
        $this->metadataBuilder->expects($this->once())->method('create')->will($this->returnValue($metadata));

        $this->assertEquals([$metadata], $this->model->getTypes());
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class WrappingMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var WrappingMapper */
    protected $wrappingMapper;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var WrappingBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingBuilderMock;

    protected function setUp()
    {
        $this->wrappingBuilderMock = $this->getMock(
            'Magento\GiftWrapping\Service\V1\Data\WrappingBuilder',
            [],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wrappingMapper = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Service\V1\Data\WrappingMapper',
            [
                'wrappingBuilder' => $this->wrappingBuilderMock
            ]
        );
    }

    public function testExtractDto()
    {
        $data = ['field' => 'data'];
        $websiteIds = [1, 2, 3];
        $imageUrl = 'url';

        /** @var \Magento\GiftWrapping\Model\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingModel */
        $wrappingModel = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        /** @var \Magento\GiftWrapping\Service\V1\Data\Wrapping|\PHPUnit_Framework_MockObject_MockObject $wrappingDto */
        $wrappingDto = $this->getMockBuilder('Magento\GiftWrapping\Service\V1\Data\Wrapping')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $wrappingModel->expects($this->once())->method('getData')->will($this->returnValue($data));
        $this->wrappingBuilderMock->expects($this->once())->method('populateWithArray')->with($data);
        $wrappingModel->expects($this->once())->method('getWebsiteIds')->will($this->returnValue($websiteIds));
        $this->wrappingBuilderMock->expects($this->once())->method('setWebsiteIds')->with($websiteIds);
        $wrappingModel->expects($this->once())->method('getImageUrl')->will($this->returnValue($imageUrl));
        $this->wrappingBuilderMock->expects($this->once())->method('setImageUrl')->with($imageUrl);
        $this->wrappingBuilderMock->expects($this->once())->method('create')->will($this->returnValue($wrappingDto));

        $this->assertSame($wrappingDto, $this->wrappingMapper->extractDto($wrappingModel));

    }
}

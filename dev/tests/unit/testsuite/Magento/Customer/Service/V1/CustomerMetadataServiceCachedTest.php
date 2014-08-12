<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;

class CustomerMetadataServiceCachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataService
     */
    private $customerMetadataServiceMock;

    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceCached
     */
    private $cachedMetadataService;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->customerMetadataServiceMock = $this->getMockBuilder(
            'Magento\Customer\Service\V1\CustomerMetadataService'
        )->setMethods([
            'getAttributes',
            'getAttributeMetadata',
            'getAllAttributesMetadata',
            'getCustomAttributesMetadata'
        ])->disableOriginalConstructor()->getMock();

        $this->cachedMetadataService = $this->objectManager->getObject(
            'Magento\Customer\Service\V1\CustomerMetadataServiceCached',
            ['metadataService' => $this->customerMetadataServiceMock]
        );
    }

    public function testGetAttributes() {
        $formCode = 'f';
        $value = 'v';

        $this->customerMetadataServiceMock->expects($this->once())
            ->method('getAttributes')
            ->with($formCode)
            ->will($this->returnValue($value));

        for ($c = 0; $c < 10; $c++) {
            $actualValue = $this->cachedMetadataService->getAttributes($formCode);
            $this->assertEquals($value, $actualValue);
        }
    }

    public function testGetAttributeMetadata() {
        $attributeCode = 'a';
        $value = 'v';

        $this->customerMetadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with($attributeCode)
            ->will($this->returnValue($value));

        for ($c = 0; $c < 10; $c++) {
            $actualValue = $this->cachedMetadataService->getAttributeMetadata($attributeCode);
            $this->assertEquals($value, $actualValue);
        }
    }

    public function testGetAttributeMetadataWithException() {
        $attributeCode = 'a';
        $value = 'v';

        $this->customerMetadataServiceMock->expects($this->exactly(10))
            ->method('getAttributeMetadata')
            ->with($attributeCode)
            ->will($this->throwException(new \Magento\Framework\Exception\NoSuchEntityException()));

        for ($c = 0; $c < 10; $c++) {
            $exceptionThrown = false;
            try {
                $this->cachedMetadataService->getAttributeMetadata($attributeCode);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }

    public function testGetAllAttributesMetadata() {
        $value = 'v';

        $this->customerMetadataServiceMock->expects($this->once())
            ->method('getAllAttributesMetadata')
            ->will($this->returnValue($value));

        for ($c = 0; $c < 10; $c++) {
            $actualValue = $this->cachedMetadataService->getAllAttributesMetadata();
            $this->assertEquals($value, $actualValue);
        }
    }

    public function testGetCustomAttributesMetadata() {
        $value = 'v';

        $this->customerMetadataServiceMock->expects($this->once())
            ->method('getCustomAttributesMetadata')
            ->will($this->returnValue($value));

        for ($c = 0; $c < 10; $c++) {
            $actualValue = $this->cachedMetadataService->getCustomAttributesMetadata();
            $this->assertEquals($value, $actualValue);
        }
    }
}

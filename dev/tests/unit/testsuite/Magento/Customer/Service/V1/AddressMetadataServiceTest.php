<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;

class AddressMetadataServiceTest extends CustomerMetadataServiceTest
{
    public function setUp()
    {
        parent::setUp();
        $this->service = $this->objectManager->getObject(
            'Magento\Customer\Service\V1\AddressMetadataService',
            [
                'attributeMetadataDataProvider' => $this->attributeMetadataDataProvider,
                'attributeMetadataConverter' => $this->attributeMetadataConverter
            ]
        );
    }

    public function testGetAttributeMetadataWithoutAttributeMetadata()
    {
        $this->attributeMetadataDataProvider
            ->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue(false));

        try {
            $this->service->getAttributeMetadata('attributeId');
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $e) {
            $this->assertSame(
                "No such entity with entityType = customer_address, attributeCode = attributeId",
                $e->getMessage()
            );
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;

class AddressMetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerAccountServiceInterface */
    private $_customerAccountService;

    /** @var AddressMetadataServiceInterface */
    private $_service;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->configure(
            [
                'Magento\Framework\Service\Config\Reader' => [
                    'arguments' => [
                        'fileResolver' => ['instance' => 'Magento\Customer\Service\V1\FileResolverStub']
                    ]
                ]
            ]
        );
        $this->_customerAccountService = $objectManager->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
        $this->_service = $objectManager->create('Magento\Customer\Service\V1\AddressMetadataServiceInterface');
    }

    public function testGetCustomAttributesMetadata()
    {
        $customAttributesMetadata = $this->_service->getCustomAttributesMetadata();
        $this->assertCount(2, $customAttributesMetadata, "Invalid number of attributes returned.");
        $configAttributeCode = 'address_attribute_1';
        $configAttributeFound = false;
        foreach ($customAttributesMetadata as $attribute) {
            if ($attribute->getAttributeCode() == $configAttributeCode) {
                $configAttributeFound = true;
                break;
            }
        }
        if (!$configAttributeFound) {
            $this->fail("Custom attribute declared in the config not found.");
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_address_custom_attribute.php
     */
    public function testGetCustomAttributesMetadataWithAttributeNamedCustomAttribute()
    {
        $customAttributesMetadata = $this->_service->getCustomAttributesMetadata();
        $customAttributeCode = 'custom_attribute';
        $customAttributeFound = false;
        $customAttributesCode = 'custom_attributes';
        $customAttributesFound = false;
        foreach ($customAttributesMetadata as $attribute) {
            if ($attribute->getAttributeCode() == $customAttributeCode) {
                $customAttributeFound = true;
            }
            if ($attribute->getAttributeCode() == $customAttributesCode) {
                $customAttributesFound = true;
            }
        }
        if (!$customAttributeFound) {
            $this->fail("Custom attribute declared in the config not found.");
        }
        if (!$customAttributesFound) {
            $this->fail("Custom attributes declared in the config not found.");
        }
        $this->assertCount(4, $customAttributesMetadata, "Invalid number of attributes returned.");
    }

    public function testGetAddressAttributeMetadata()
    {
        $vatValidMetadata = $this->_service->getAttributeMetadata('vat_is_valid');

        $this->assertNotNull($vatValidMetadata);
        $this->assertEquals('vat_is_valid', $vatValidMetadata->getAttributeCode());
        $this->assertEquals('text', $vatValidMetadata->getFrontendInput());
        $this->assertEquals('VAT number validity', $vatValidMetadata->getStoreLabel());
    }

    public function testGetAddressAttributeMetadataNoSuchEntity()
    {
        try {
            $this->_service->getAttributeMetadata('1');
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $e) {
            $this->assertEquals(
                'No such entity with entityType = customer_address, attributeCode = 1',
                $e->getMessage()
            );
        }
    }

    public function testGetAttributes()
    {
        $formAttributesMetadata = $this->_service->getAttributes('customer_address_edit');
        $this->assertCount(15, $formAttributesMetadata, "Invalid number of attributes for the specified form.");

        /** Check some fields of one attribute metadata */
        $attributeMetadata = $formAttributesMetadata['company'];
        $this->assertInstanceOf('Magento\Customer\Service\V1\Data\Eav\AttributeMetadata', $attributeMetadata);
        $this->assertEquals('company', $attributeMetadata->getAttributeCode(), 'Attribute code is invalid');
        $this->assertNotEmpty($attributeMetadata->getValidationRules(), 'Validation rules are not set');
        $this->assertEquals('varchar', $attributeMetadata->getBackendType(), 'Backend type is invalid');
        $this->assertEquals('Company', $attributeMetadata->getFrontendLabel(), 'Frontend label is invalid');
    }
}

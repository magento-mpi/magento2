<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;

class CustomerMetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerAccountServiceInterface */
    private $_customerAccountService;

    /** @var CustomerMetadataServiceInterface */
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
        $this->_service = $objectManager->create('Magento\Customer\Service\V1\CustomerMetadataServiceInterface');
    }

    public function testGetCustomAttributesMetadata()
    {
        $customAttributesMetadata = $this->_service->getCustomAttributesMetadata();
        $this->assertCount(3, $customAttributesMetadata, "Invalid number of attributes returned.");
        $configAttributeCode = 'customer_attribute_1';
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
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerAttributeMetadata()
    {
        // Expect these attributes to exist but do not check the value
        $expectAttrsWOutVals = array('created_at');

        // Expect these attributes to exist and check the value - values come from _files/customer.php
        $expectAttrsWithVals = array(
            'id' => '1',
            'website_id' => '1',
            'store_id' => '1',
            'group_id' => '1',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'customer@example.com',
            'default_billing' => '1',
            'default_shipping' => '1',
            'disable_auto_group_change' => '0'
        );

        $customer = $this->_customerAccountService->getCustomer(1);
        $this->assertNotNull($customer);

        $attributes = \Magento\Framework\Service\EavDataObjectConverter::toFlatArray($customer);
        $this->assertNotEmpty($attributes);

        foreach ($attributes as $attributeCode => $attributeValue) {
            $this->assertNotNull($attributeCode);
            $this->assertNotNull($attributeValue);
            $attributeMetadata = $this->_service->getAttributeMetadata($attributeCode);
            $attrMetadataCode = $attributeMetadata->getAttributeCode();
            $this->assertSame($attributeCode, $attrMetadataCode);
            if (($key = array_search($attrMetadataCode, $expectAttrsWOutVals)) !== false) {
                unset($expectAttrsWOutVals[$key]);
            } else {
                $this->assertArrayHasKey($attrMetadataCode, $expectAttrsWithVals);
                $this->assertSame(
                    $expectAttrsWithVals[$attrMetadataCode],
                    $attributeValue,
                    "Failed for {$attrMetadataCode}"
                );
                unset($expectAttrsWithVals[$attrMetadataCode]);
            }
        }
        $this->assertEmpty($expectAttrsWOutVals);
        $this->assertEmpty($expectAttrsWithVals);
    }

    public function testGetCustomerAttributeMetadataNoSuchEntity()
    {
        try {
            $this->_service->getAttributeMetadata('20');
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $e) {
            $this->assertEquals('No such entity with entityType = customer, attributeCode = 20', $e->getMessage());
        }
    }
}

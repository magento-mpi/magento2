<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto;

use Magento\TestFramework\Helper\Bootstrap;

class AddressBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Service\V1\Dto\AddressBuilder */
    protected $_addressBuilder;

    protected function setUp()
    {
        $this->_addressBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Dto\AddressBuilder'
        );
        parent::setUp();
    }

    /**
     * Two custom attributes are created, one for customer and another for customer address.
     *
     * Attribute related to customer address should be returned only.
     *
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_address.php
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_customer.php
     */
    public function testGetCustomAttributeCodes()
    {
        $userDefinedAttributeCode = 'address_user_attribute';
        $attributeCodes = $this->_addressBuilder->getCustomAttributeCodes();
        $expectedAttributes = [$userDefinedAttributeCode, 'prefix', 'middlename', 'suffix'];
        $this->assertEquals($expectedAttributes, $attributeCodes, 'Custom attribute codes list is invalid.');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\TestFramework\Helper\Bootstrap;

class AddressBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Service\V1\Data\AddressBuilder */
    protected $_addressBuilder;

    protected function setUp()
    {
        $this->_addressBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\AddressBuilder'
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
    public function testGetCustomAttributesCodes()
    {
        $this->markTestSkipped('Will be fixed in scope of MAGETWO-27167');
        $userDefinedAttributeCode = 'address_user_attribute';
        $attributeCodes = $this->_addressBuilder->getCustomAttributesCodes();
        $expectedAttributes = [$userDefinedAttributeCode];
        $this->assertEquals($expectedAttributes, $attributeCodes, 'Custom attribute codes list is invalid.');
    }
}

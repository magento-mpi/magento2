<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\TestFramework\Helper\Bootstrap;

class CustomerBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder */
    protected $_customerBuilder;

    protected function setUp()
    {
        $this->_customerBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerBuilder'
        );
        parent::setUp();
    }

    /**
     * Two custom attributes are created, one for customer and another for customer address.
     *
     * Attribute related to customer should be returned only.
     *
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_address.php
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_customer.php
     */
    public function testGetCustomAttributesCodes()
    {
        $userDefinedAttributeCode = FIXTURE_ATTRIBUTE_USER_DEFINED_CUSTOMER_NAME;
        $attributeCodes = $this->_customerBuilder->getCustomAttributesCodes();
        $expectedAttributes = [
            'disable_auto_group_change',
            'prefix',
            'middlename',
            'suffix',
            'created_at',
            'dob',
            'taxvat',
            'gender',
            $userDefinedAttributeCode
        ];
        $this->assertEquals($expectedAttributes, $attributeCodes, 'Custom attribute codes list is invalid.');
    }
}

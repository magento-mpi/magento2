<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto;

/**
 * Customer
 *
 * @package Magento\Customer\Service\Entity\V1
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    const CONFIRMATION = 'a4fg7h893e39d';
    const CREATED_AT = '2013-11-05';
    const STORE_NAME = 'Store Name';
    const DOB = '1970-01-01';
    const GENDER = 'Male';
    const GROUP_ID = 1;
    const MIDDLENAME = 'A';
    const PREFIX = 'Mr.';
    const STORE_ID = 1;
    const SUFFIX = 'Esq.';
    const TAXVAT = '12';
    const WEBSITE_ID = 1;

    /** Sample values for testing */
    const ID = 1;
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const NAME = 'J';
    const EMAIL = 'janedoe@example.com';
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_VALUE = 'attribute_value';

    public function testSetters()
    {
        $customerData = $this->_createCustomerData();
        $customerBuilder = new \Magento\Customer\Service\V1\Dto\CustomerBuilder();
        $customerBuilder->populateWithArray($customerData);

        /** @var Customer $customer */
        $customer = $customerBuilder->create();

        $this->assertEquals(self::ID, $customer->getCustomerId());
        $this->assertEquals(self::FIRSTNAME, $customer->getFirstname());
        $this->assertEquals(self::LASTNAME, $customer->getLastname());
        $this->assertEquals(self::EMAIL, $customer->getEmail());
        $this->assertEquals(self::CONFIRMATION, $customer->getConfirmation());
        $this->assertEquals(self::CREATED_AT, $customer->getCreatedAt());
        $this->assertEquals(self::STORE_NAME, $customer->getCreatedIn());
        $this->assertEquals(self::DOB, $customer->getDob());
        $this->assertEquals(self::GENDER, $customer->getGender());
        $this->assertEquals(self::GROUP_ID, $customer->getGroupId());
        $this->assertEquals(self::MIDDLENAME, $customer->getMiddlename());
        $this->assertEquals(self::PREFIX, $customer->getPrefix());
        $this->assertEquals(self::STORE_ID, $customer->getStoreId());
        $this->assertEquals(self::SUFFIX, $customer->getSuffix());
        $this->assertEquals(self::TAXVAT, $customer->getTaxvat());
        $this->assertEquals(self::WEBSITE_ID, $customer->getWebsiteId());
        $attribute = $customer->getAttribute(self::ATTRIBUTE_CODE);
        $this->assertEquals(self::ATTRIBUTE_VALUE, $attribute);
    }

    public function testGetAttributeNotExist()
    {
        $customerData = $this->_createCustomerData();
        $customerBuilder = new \Magento\Customer\Service\V1\Dto\CustomerBuilder();
        $customerBuilder->populateWithArray($customerData);
        /** @var Customer $customer */
        $customer = $customerBuilder->create();

        $this->assertNull($customer->getAttribute('A non existing attribute code'));
    }

    public function testGetAttributes()
    {
        $customerData = $this->_createCustomerData();
        /** @var CustomerBuilder $customerBuilder */
        $customerBuilder = new \Magento\Customer\Service\V1\Dto\CustomerBuilder();
        $customerBuilder->populateWithArray($customerData);
        /** @var Customer $customer */
        $customer = $customerBuilder->create();

        $actualAttributes = $customer->getAttributes();
        $this->assertEquals(
            [
                'confirmation' => self::CONFIRMATION,
                'created_at' => self::CREATED_AT,
                'created_in' => self::STORE_NAME,
                'dob' => self::DOB,
                'email' => self::EMAIL,
                'firstname' => self::FIRSTNAME,
                'gender' => self::GENDER,
                'group_id' => self::GROUP_ID,
                'lastname' => self::LASTNAME,
                'middlename' => self::MIDDLENAME,
                'prefix' => self::PREFIX,
                'store_id' => self::STORE_ID,
                'suffix' => self::SUFFIX,
                'taxvat' => self::TAXVAT,
                'website_id' => self::WEBSITE_ID,
                self::ATTRIBUTE_CODE => self::ATTRIBUTE_VALUE,
            ],
            $actualAttributes
        );
    }

    /**
     * Create customer using setters.
     *
     * @return CustomerBuilder
     */
    private function _createCustomerData()
    {
        return [
            self::ATTRIBUTE_CODE => self::ATTRIBUTE_VALUE,
            'id' => self::ID,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'email' => self::EMAIL,
            'confirmation' => self::CONFIRMATION,
            'created_at' => self::CREATED_AT,
            'created_in' => self::STORE_NAME,
            'dob' => self::DOB,
            'gender' => self::GENDER,
            'group_id' => self::GROUP_ID,
            'middlename' => self::MIDDLENAME,
            'prefix' => self::PREFIX,
            'store_id' => self::STORE_ID,
            'suffix' => self::SUFFIX,
            'taxvat' => self::TAXVAT,
            'website_id' => self::WEBSITE_ID
        ];
    }
}

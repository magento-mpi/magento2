<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\Entity\V1;

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
        /** @var Customer $customer */
        $customer = $this->_createCustomerWithSetters();

        $this->assertEquals(self::ID, $customer->getCustomerId());
        $this->assertEquals(self::FIRSTNAME, $customer->getFirstName());
        $this->assertEquals(self::LASTNAME, $customer->getLastName());
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
        /** @var Customer $customer */
        $customer = $this->_createCustomerWithSetters();
        $this->assertNull($customer->getAttribute('A non existing attribute code'));
    }

    /**
     * @dataProvider setNonattributeDataProvider
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage Cannot set or change attribute
     */
    public function testSetNonattribute($attrName, $attrValue)
    {
        /** @var Customer $customer */
        $customer = $this->_createCustomerWithSetters();
        $customer->setAttribute($attrName, $attrValue);
    }

    /**
     * Dataprovider which returns forbidden customer attributes
     *
     * @return array
     */
    public function setNonattributeDataProvider()
    {
        return [
            ['id', 1],
        ];
    }

    public function testGetAttributes()
    {
        /** @var Customer $customer */
        $customer = $this->_createCustomerWithSetters();
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
     * @return Customer
     */
    private function _createCustomerWithSetters()
    {
        /** @var Customer $customer */
        $customer = new Customer();
        $customer->setCustomerId(self::ID)
            ->setFirstName(self::FIRSTNAME)
            ->setLastName(self::LASTNAME)
            ->setEmail(self::EMAIL)
            ->setConfirmation(self::CONFIRMATION)
            ->setCreatedAt(self::CREATED_AT)
            ->setCreatedIn(self::STORE_NAME)
            ->setDob(self::DOB)
            ->setGender(self::GENDER)
            ->setGroupId(self::GROUP_ID)
            ->setMiddlename(self::MIDDLENAME)
            ->setPrefix(self::PREFIX)
            ->setStoreId(self::STORE_ID)
            ->setSuffix(self::SUFFIX)
            ->setTaxvat(self::TAXVAT)
            ->setWebsiteId(self::WEBSITE_ID)
            ->setAttributes([self::ATTRIBUTE_CODE => self::ATTRIBUTE_VALUE]);
        return $customer;
    }
}

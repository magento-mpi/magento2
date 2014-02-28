<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

/**
 * Customer
 *
 * @package Magento\Customer\Service\V1\Data
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

    /** @var  \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder */
    protected $_customerBuilder;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $customerMetadataService = $this->getMockForAbstractClass(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface', [], '', false
        );
        $customerMetadataService
            ->expects($this->any())
            ->method('getCustomCustomerAttributeMetadata')
            ->will($this->returnValue([]));
        $this->_customerBuilder = $this->_objectManager->getObject('Magento\Customer\Service\V1\Data\CustomerBuilder',
            ['metadataService' => $customerMetadataService]
        );
    }

    public function testSetters()
    {
        $customerData = $this->_createCustomerData();
        $customer = $this->_customerBuilder->populateWithArray($customerData)
            ->create();
        $this->assertEquals(self::ID, $customer->getId());
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
    }

    public function testGetAttributes()
    {
        $customerData = $this->_createCustomerData();
        $customer = $this->_customerBuilder->populateWithArray($customerData)
            ->create();

        $actualAttributes = \Magento\Convert\ConvertArray::toFlatArray($customer->__toArray());
        $this->assertEquals(
            [
                'id' => self::ID,
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
                'website_id' => self::WEBSITE_ID
            ],
            $actualAttributes
        );
    }

    public function testGetCustomAttributes()
    {
        $customAttributes = [
            'custom_attribute1' => 'value1',
            'custom_attribute2' => 'value2'
        ];
        $customerData = [
            'attribute1' => 'value1',
            Customer::CUSTOM_ATTRIBUTES_KEY => $customAttributes
        ];
        $customerDataObject = $this->_customerBuilder->populateWithArray($customerData)->create();
        $this->assertEquals(
            $customAttributes,
            $customerDataObject->getCustomAttributes(),
            'Invalid custom attributes.'
        );
    }

    public function testPopulateFromPrototypeVsArray()
    {
        $customerFromArray = $this->_customerBuilder->populateWithArray([
            Customer::FIRSTNAME => self::FIRSTNAME,
            Customer::LASTNAME  => self::LASTNAME,
            Customer::EMAIL     => self::EMAIL,
            Customer::ID        => self::ID,
            'entity_id'         => self::ID,
        ])->create();
        $customerFromPrototype = $this->_customerBuilder->populate($customerFromArray)->create();

        $this->assertEquals($customerFromArray->__toArray(), $customerFromPrototype->__toArray());
    }

    public function testPopulateFromCustomerIdInArray()
    {
        $customer = $this->_customerBuilder->populateWithArray([
            Customer::FIRSTNAME => self::FIRSTNAME,
            Customer::LASTNAME  => self::LASTNAME,
            Customer::EMAIL     => self::EMAIL,
            Customer::ID        => self::ID,
        ])->create();

        $this->assertEquals(self::FIRSTNAME, $customer->getFirstname());
        $this->assertEquals(self::LASTNAME, $customer->getLastname());
        $this->assertEquals(self::EMAIL, $customer->getEmail());
        $this->assertEquals(self::ID, $customer->getId());
    }

    /**
     * Create customer using setters.
     *
     * @return array
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

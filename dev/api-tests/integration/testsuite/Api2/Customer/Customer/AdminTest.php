<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test for customer API2 by admin api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customer_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Customer model instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Customer attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Required customer attributes
     * @var array
     */
    protected $_requiredAttributes;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->_initCustomer();
    }

    /**
     * Init customer model instance
     *
     * @return Api2_Customer_Customer_AdminTest
     */
    protected function _initCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = require dirname(__FILE__) . '/../../../../fixture/Customer/Customer.php';
            $this->_customer->addData(array(
                'password'   => '123123q',
                'website_id' => 1,
                'group_id'   => 1
            ))->save();
            $this->addModelToDelete($this->_customer, true);
        }

        return $this;
    }

    /**
     * Get customer attributes and filter required attributes in it
     * Set attributes to class properties
     *
     * @return Api2_Customer_Customer_AdminTest
     */
    protected function _initAttributes()
    {
        if (null === $this->_customer) {
            throw new Exception('A customer was not instantiated.');
        }
        if (null === $this->_requiredAttributes) {
            $this->_attributes = $this->_customer->getAttributes();
            foreach ($this->_attributes as $attribute) {
                $label = $attribute->getFrontendLabel();
                if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
                    $this->_requiredAttributes[$attribute->getAttributeCode()] = $label;
                }
            }
        }
        return $this;
    }

    /**
     * Test create customer
     *
     * @resourceOperation customer::create
     */
    public function testCreate()
    {
        $response = $this->callPost('customers/1', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }

    /**
     * Test retrieve existing customer data
     *
     * @resourceOperation customer::get
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers/' . $this->_customer->getId());

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        $responseData = $response->getBody();
        $this->assertNotEmpty($responseData);

        foreach ($responseData as $field => $value) {
            $this->assertEquals($this->_customer->getData($field), $value);
        }
    }

    /**
     * Test retrieve not existing customer
     *
     * @resourceOperation customer::get
     */
    public function testRetrieveUnavailableResource()
    {
        $response = $this->callGet('customers/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test update customer
     *
     * @resourceOperation customer::update
     */
    public function testUpdate()
    {
        $putData = array(
            'firstname' => 'Salt',
            'lastname'  => 'Pepper',
            'email'     => mt_rand() . 'newemail@example.com',
            'website_id' => 1,
            'group_id'   => 1
        );
        $response = $this->callPut('customers/' . $this->_customer->getId(), $putData);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        // Reload customer
        $this->_customer->load($this->_customer->getId());

        foreach ($putData as $field => $value) {
            $this->assertEquals($this->_customer->getData($field), $value);
        }
    }

    /**
     * Test filter data in update customer
     *
     * @resourceOperation customer::update
     */
    public function testUpdateFilter()
    {
        /** @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = $this->_customer->getAttribute('firstname');
        $oldFilterValue = $attribute->getInputFilter('striptags');
        $attribute->setInputFilter('striptags')->save();

        $putData = array(
            'firstname'  => 'testFirstname<b>Test</b>',
            'lastname'   => $this->_customer->getFirstname(),
            'email'      => $this->_customer->getEmail(),
            'website_id' => $this->_customer->getWebsiteId(),
            'group_id'   => $this->_customer->getGroupId()
        );
        $response = $this->callPut('customers/' . $this->_customer->getId(), $putData);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        /** @var $model Mage_Customer_Model_Customer */
        $model = Mage::getModel('Mage_Customer_Model_Customer');

        // Reload customer
        $model->load($this->_customer->getId());

        $this->assertEquals($model->getFirstname(), 'testFirstnameTest');

        // Restore attribute filter value
        $attribute->setInputFilter($oldFilterValue)->save();
    }

    /**
     * Test update customer with empty required fields
     *
     * @param string $attributeCode
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer::update
     */
    public function testUpdateEmptyRequiredField($attributeCode)
    {
        $this->_customer->setData($attributeCode, '');

        $response = $this->callPut('customers/' . $this->_customer->getId(), $this->_customer->getData());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $response->getStatus());
        $responseData = $response->getBody();

        $this->assertArrayHasKey('messages', $responseData, "The response doesn't has messages.");
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Data provider for testCreateEmptyRequiredField and testCreateWithoutRequiredField
     * (dataSet number depends of number of required attributes)
     *
     * @dataSetNumber 4
     * @return array
     */
    public function providerRequiredAttributes()
    {
        $this->_initCustomer()
            ->_initAttributes();

        $fields = array_keys($this->_requiredAttributes);
        $output = array();

        foreach ($fields as $field) {
            if ('website_id' !== $field) {
                $output[] = array($field);
            }
        }

        return $output;
    }

    /**
     * Test update not existing customer
     *
     * @resourceOperation customer::update
     */
    public function testUpdateUnavailableResource()
    {
        $response = $this->callPut('customers/invalid_id', array(
            'firstname'  => 'TestFirstname',
            'lastname'   => 'TestLastname',
            'email'      => 'testemail@example.com',
            'website_id' => 1,
            'group_id'   => 1
        ));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test unsuccessful update of drop down attributes. Check customer drop down fields validation
     *
     * @dataProvider dataProviderForUpdateDropDownsWithInvalidValues
     * @resourceOperation customer::update
     * @param string $field
     * @param mixed $value
     */
    public function testUpdateDropDownsWithInvalidValues($field, $value)
    {
        $customerData = array($field => $value);
        $response = $this->callPut('customers/' . $this->_customer->getId(), $customerData);
        $responseData = $response->getBody();
        $this->assertArrayHasKey('messages', $responseData, "Response must have messages.");
        $this->assertArrayHasKey('error', $responseData['messages'], "Response must contain errors.");
        $expectedErrors = array("Invalid value \"$value\" for $field", "Resource data pre-validation error.");
        $errors = $responseData['messages']['error'];
        foreach ($errors as $error) {
            $this->assertTrue(in_array($error['message'], $expectedErrors),
                'Error message is invalid: ' . $error['message']);
        }
    }

    /**
     * Provider of invalid drop down fields values
     *
     * @dataSetNumber 8
     * @return array
     */
    public function dataProviderForUpdateDropDownsWithInvalidValues()
    {
        return array(
            array('gender', -1),
            array('gender', 0),
            array('gender', 3),
            array('group_id', -1),
            array('group_id', 0),
            array('group_id', 4),
            array('group_id', 'invalid'),
            array('group_id', 'invalid'),
        );
    }

    /**
     * Test successful drop down attributes update.
     * Check if validation works correct with values of different types (int, string)
     *
     * @dataProvider dataProviderForUpdateDropDownsWithValidValues
     * @resourceOperation customer::update
     * @param $customerData
     */
    public function testUpdateDropDownsWithValidValues($customerData)
    {
        $response = $this->callPut('customers/' . $this->_customer->getId(), $customerData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());
    }

    /**
     * Provider of valid drop down fields values
     *
     * @dataSetNumber 2
     * @return array
     */
    public function dataProviderForUpdateDropDownsWithValidValues()
    {
        $dropdownData = array(
            array(array('website_id' => 1, 'group_id' => 1, 'gender' => 1)),
            array(array('website_id' => '1', 'group_id' => '1', 'gender' => '1')),
        );
        return $dropdownData;
    }

    /**
     * Test delete customer
     *
     * @resourceOperation customer::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers/' . $this->_customer->getId());

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        /** @var $model Mage_Customer_Model_Customer */
        $model = Mage::getModel('Mage_Customer_Model_Customer')->load($this->_customer->getId());
        $this->assertEmpty($model->getId());
    }

    /**
     * Test delete not existing customer
     *
     * @resourceOperation customer::delete
     */
    public function testDeleteUnavailableResource()
    {
        $response = $this->callDelete('customers/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for customer API2 by customer api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customer_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Customer model instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Other customer model instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_otherCustomer;

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

        $this->_customer = $this->getDefaultCustomer();

        $this->_otherCustomer = require dirname(__FILE__) . '/../../../../fixtures/Customer/Customer.php';
        $this->_otherCustomer->save();

        $this->addModelToDelete($this->_otherCustomer, true);
    }

    /**
     * Init customer model instance
     *
     * @return Api2_Customer_Customer_CustomerTest
     */
    protected function _initCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = $this->getDefaultCustomer();
        }
        return $this;
    }

    /**
     * Get customer required attributes
     * Set attributes to class properties
     *
     * @return Api2_Customer_Customers_AdminTest
     */
    protected function _initRequiredAttributes()
    {
        if (null === $this->_customer) {
            throw new Exception('A customer was not instantiated.');
        }
        if (null === $this->_requiredAttributes) {
            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            // when customer edit his/her own information used customer_account_edit
            $customerForm->setFormCode('customer_account_create')->setEntity($this->_customer);

            $this->_requiredAttributes = array();
            foreach ($customerForm->getAttributes() as $attribute) {
                if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
                    $this->_requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
                }
            }
        }
        return $this;
    }

    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('customers/1', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve existing customer data
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
     * Test retrieve another customer
     */
    public function testRetrieveUnavailableResource()
    {
        $response = $this->callGet('customers/' . $this->_otherCustomer->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test update customer
     */
    public function testUpdate()
    {
        $putData = array(
            'firstname' => 'Oleg',
            'lastname'  => 'Barabash',
            'email'     => $this->_customer->getEmail()
        );
        $response = $this->callPut('customers/' . $this->_customer->getId(), $putData);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        /** @var $model Mage_Customer_Model_Customer */
        $model = Mage::getModel('customer/customer');

        // Reload customer
        $model->load($this->_customer->getId());

        foreach ($putData as $field => $value) {
            $this->assertEquals($model->getData($field), $value);
        }

        // Restore firstname and lastname attribute values
        $model->addData(array(
            'firstname' => $this->_customer->getFirstname(),
            'lastname'  => $this->_customer->getLastname(),
        ))->save();
    }

    /**
     * Test filter data in update customer
     */
    public function testUpdateFilter()
    {
        /** @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = $this->_customer->getAttribute('firstname');
        $oldFilterValue = $attribute->getInputFilter('striptags');
        $attribute->setInputFilter('striptags')->save();

        $putData = array(
            'firstname'  => 'testFirstname<b>Test</b>',
            'lastname'   => $this->_customer->getLastname(),
            'email'      => $this->_customer->getEmail()
        );
        $response = $this->callPut('customers/' . $this->_customer->getId(), $putData);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        /** @var $model Mage_Customer_Model_Customer */
        $model = Mage::getModel('customer/customer');

        // Reload customer
        $model->load($this->_customer->getId());

        $this->assertEquals($model->getFirstname(), 'testFirstnameTest');

        // Restore middlename
        $model->setFirstname($this->_customer->getFirstname())->save();

        // Restore attribute filter value
        $attribute->setInputFilter($oldFilterValue)->save();
    }

    /**
     * Test update customer with empty required fields
     *
     * @param string $attributeCode
     * @dataProvider providerRequiredAttributes
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
     * Test update customer withous required fields
     *
     * @param string $attributeCode
     * @dataProvider providerRequiredAttributes
     */
    public function testUpdateWithoutRequiredField($attributeCode)
    {
        $this->_customer->unsetData($attributeCode);

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
     *
     * @return array
     */
    public function providerRequiredAttributes()
    {
        $this->_initCustomer()
            ->_initRequiredAttributes();

        $fields = array_keys($this->_requiredAttributes);
        $output = array();

        foreach ($fields as $field) {
            $output[] = array($field);
        }

        return $output;
    }

    /**
     * Test update another customer
     */
    public function testUpdateUnavailableResource()
    {
        $response = $this->callPut('customers/' . $this->_otherCustomer->getId(), array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test delete existing customer data
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers/1');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }
}

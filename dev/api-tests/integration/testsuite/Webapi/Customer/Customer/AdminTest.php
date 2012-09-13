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
 * Test for customer Webapi by admin api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Customer_Customer_AdminTest extends Magento_Test_Webservice_Rest_Admin
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
     * @return Webapi_Customer_Customer_AdminTest
     */
    protected function _initCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = require dirname(__FILE__) . '/../../../../fixture/_block/Customer/Customer.php';
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
     * @return Webapi_Customer_Customer_AdminTest
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
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }

    /**
     * Test retrieve existing customer data
     *
     * @resourceOperation customer::get
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers/' . $this->_customer->getId());

        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $response->getStatus());

        $responseData = $response->getBody();
        $this->assertNotEmpty($responseData);

        $this->assertCount(22, $responseData, 'Invalid fields quantity in response.');
//        foreach ($responseData as $field => $value) {
//            $message = '';
//            try {
//                $this->assertEquals($this->_customer->getData($field), $value);
//            } catch (Exception $e) {
//                $message .= "\n" . $e->getMessage();
//            }
//            if ($message) {
//                $this->markTestIncomplete("Fields filtration is not implemented: \n" . $message);
//            }
//        }
    }

    /**
     * Test retrieve not existing customer
     *
     * @resourceOperation customer::get
     */
    public function testRetrieveUnavailableResource()
    {
        $response = $this->callGet('customers/invalid_id');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test update customer
     *
     * @resourceOperation customer::update
     */
    public function testUpdate()
    {
        // TODO: The source of fail is $this->getAttributes() call in Mage_Customer_Model_Customer::validate()
        $putData = array(
            'firstname' => 'Salt',
            'lastname'  => 'Pepper',
            'email'     => mt_rand() . 'newemail@example.com',
            'website_id' => 1,
            'group_id'   => 1
        );
        $response = $this->callPut('customers/' . $this->_customer->getId(), $putData);

        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $response->getStatus());

        // Reload customer
        $updatedCustomer = Mage::getModel('Mage_Customer_Model_Customer');
        $updatedCustomer->load($this->_customer->getId());

        foreach ($putData as $field => $expectedValue) {
            $this->assertEquals($expectedValue, $updatedCustomer->getData($field));
        }
    }

    /**
     * Test delete customer
     *
     * @resourceOperation customer::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers/' . $this->_customer->getId());

        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $response->getStatus());

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
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND, $response->getStatus());
    }
}

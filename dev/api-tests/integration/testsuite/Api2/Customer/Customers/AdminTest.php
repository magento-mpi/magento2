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
 * Test for customers API2 by admin api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customers_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Customer count of collection
     */
    const CUSTOMER_COLLECTION_COUNT = 5;

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
     * @return Api2_Customer_Customers_AdminTest
     */
    protected function _initCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = require dirname(__FILE__) . '/../../../../fixture/_block/Customer/Customer.php';
            $this->_customer->addData(array(
                'password'   => '123123q',
                'website_id' => 1,
                'group_id'   => 1
            ));
        }
        return $this;
    }

    /**
     * Get customer attributes and filter required attributes in it
     * Set attributes to class properties
     *
     * @return Api2_Customer_Customers_AdminTest
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
     * Generate customers to test collection
     */
    protected function _generateCustomers()
    {
        $counter = 0;
        while ($counter++ < self::CUSTOMER_COLLECTION_COUNT) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('Mage_Customer_Model_Customer');
            $customer->setData($this->_customer)
                ->setEmail(mt_rand() . 'customer.example.com')
                ->save();

            $this->addModelToDelete($customer, true);
        }
    }

    /**
     * Test create customer
     *
     * @resourceOperation customer::create
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', $this->_customer->getData());
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $response->getStatus());
        list($customerId) = array_reverse(explode('/', $response->getHeader('Location')));

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
        $this->assertGreaterThan(0, $customer->getId());

        $this->addModelToDelete($customer, true);
    }

    /**
     * Test retrieve customer collection
     *
     * @resourceOperation customer::multiget
     */
    public function testRetrieve()
    {
        $this->_generateCustomers();

        $requestParams = array(
            Mage_Api2_Model_Request::QUERY_PARAM_ORDER_FIELD => 'entity_id',
            Mage_Api2_Model_Request::QUERY_PARAM_ORDER_DIR => Varien_Data_Collection::SORT_ORDER_ASC
        );
        $response = $this->callGet('customers', $requestParams);
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $response->getStatus());

        $customerIds = array();
        foreach ($response->getBody() as $item) {
            $this->arrayHasKey('entity_id');
            $customerIds[] = $item['entity_id'];
        }

        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = $this->_customer->getCollection();
        $collection->setOrder('entity_id', Varien_Data_Collection::SORT_ORDER_ASC);

        $this->assertSame($collection->getAllIds(Mage_Api2_Controller_Rest_ActionAbstract::PAGE_SIZE_DEFAULT), $customerIds);
    }

    /**
     * Test update customer
     *
     * @resourceOperation customer::update
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }

    /**
     * Test delete customer
     *
     * @resourceOperation customer::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }
}

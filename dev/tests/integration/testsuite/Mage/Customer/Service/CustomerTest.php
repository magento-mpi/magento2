<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test service layer Mage_Customer_Service_Customer
 */
class Mage_Customer_Service_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Service_Customer
     */
    protected $_model;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_createdCustomer;

    protected function setUp()
    {
        $this->_model = new Mage_Customer_Service_Customer();
    }

    protected function tearDown()
    {
        $previousStoreId = Mage::app()->getStore();
        Mage::app()->setCurrentStore(Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID));
        if ($this->_createdCustomer && $this->_createdCustomer->getId() > 0) {
            $this->_createdCustomer->delete();
        }
        Mage::app()->setCurrentStore($previousStoreId);

        $this->_model = null;
    }

    /**
     * Create and check customer
     *
     * @param array $customerData
     */
    protected function _createAndCheckCustomer($customerData)
    {
        $this->_createdCustomer = $this->_model->create($customerData);
        $this->assertInstanceOf('Mage_Customer_Model_Customer', $this->_createdCustomer);
        $this->assertFalse($this->_createdCustomer->isObjectNew());
        $this->assertNotEmpty($this->_createdCustomer->getId());

        $createdData = $this->_createdCustomer->toArray(array_keys($customerData));
        $this->assertEquals($createdData, $customerData);
    }

    /**
     * Update and check customer
     *
     * @param int $customerId
     * @param array $customerData
     * @param string $assertFunction
     * @param array $forbiddenFields
     * @return Mage_Customer_Model_Customer
     */
    protected function _updateAndCheckCustomer($customerId, $customerData, $assertFunction, $forbiddenFields = array())
    {
        $updatedCustomer = $this->_model->update($customerId, $customerData);
        $this->assertInstanceOf('Mage_Customer_Model_Customer', $updatedCustomer);
        $this->assertGreaterThan(0, $updatedCustomer->getId());

        foreach ($customerData as $key => $val) {
            if (!in_array($key, $forbiddenFields)) {
                $this->$assertFunction($val, $updatedCustomer->getData($key));
            }
        }

        return $updatedCustomer;
    }

    /**
     * @param array $customerData
     * @param string $exceptionName
     * @param string $exceptionText
     * @dataProvider initCreateCustomerDataProvider
     */
    public function testCreate($customerData, $exceptionName = '', $exceptionText = '')
    {
        if (!empty($exceptionName)) {
            $this->setExpectedException($exceptionName, $exceptionText);
        }

        $this->_createAndCheckCustomer($customerData);
    }

    /**
     * @return array
     */
    public function initCreateCustomerDataProvider()
    {
        return array(
            'Valid data' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => 'SomeName',
                'middlename' => null,
                'lastname' => 'SomeSurname',
                'suffix' => null,
                'email' => 'test' . mt_rand(1000, 9999) . '@mail.com',
                'dob' => null,
                'taxvat' => null,
                'gender' => 1,
                'password' => '123123q',
                'default_billing' => null,
                'default_shipping' => null,
                'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID
            )),
            'First name is required field' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => null,
                'lastname' => 'SomeSurname',
                'suffix' => null,
                'email' => 'test' . mt_rand(1000, 9999) . '@mail.com',
                'password' => '123123q',
                'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID
            ), 'Magento_Validator_Exception'),
            'Invalid email' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => 'SomeName',
                'lastname' => 'SomeSurname',
                'suffix' => null,
                'email' => '111@111',
                'password' => '123123q',
                'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID
            ), 'Magento_Validator_Exception'),
            'Invalid password' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => 'SomeName',
                'lastname' => 'SomeSurname',
                'suffix' => null,
                'email' => 'test' . mt_rand(1000, 9999) . '@mail.com',
                'password' => '123',
                'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID
            ), 'Mage_Eav_Model_Entity_Attribute_Exception', 'The password must have at least 6 characters.'),
            'Read-only entity_id' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'firstname' => 'SomeName',
                'lastname' => 'SomeSurname',
                'email' => 'test' . mt_rand(1000, 9999) . '@mail.com',
                'gender' => 1,
                'password' => '123123q',
                'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID,
                'entity_id' => 1
            ), 'Magento_Validator_Exception', 'Read-only property cannot be changed'),
        );
    }

    /**
     * @param array $customerData
     * @param string $exceptionName
     * @param string $exceptionMessage
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @dataProvider initUpdateCustomerDataProvider
     */
    public function testUpdate($customerData, $exceptionName = '', $exceptionMessage = '')
    {
        $expected = new Mage_Customer_Model_Customer();
        $expected->load(1);

        if (!empty($exceptionName)) {
            $this->setExpectedException($exceptionName, $exceptionMessage);
        }

        $this->_updateAndCheckCustomer($expected->getId(),
            $customerData, 'assertEquals');
    }

    /**
     * @return array
     */
    public function initUpdateCustomerDataProvider()
    {
        return array(
            'Change name' => array(array(
                'firstname' => 'SomeName2',
            )),
            'Change password' => array(array(
                'password' => '111111',
            )),
            'Invalid password' => array(array(
                'password' => '111'
            ), 'Mage_Eav_Model_Entity_Attribute_Exception'),
            'Invalid name' => array(array(
                'firstname' => null
            ), 'Magento_Validator_Exception'),
            'Invalid email' => array(array(
                'email' => '3434@23434'
            ), 'Magento_Validator_Exception'),
            'Read-only website_id' => array(array(
                'website_id' => 111
            ), 'Magento_Validator_Exception', 'Read-only property cannot be changed.'),
            'Read-only entity_type_id' => array(array(
                'entity_type_id' => 555
            ), 'Magento_Validator_Exception', 'Read-only property cannot be changed.'),
            'Read-only created_in' => array(array(
                'created_in' => 1
            ), 'Magento_Validator_Exception', 'Read-only property cannot be changed.'),
            'Read-only store_id' => array(array(
                'store_id' => 15
            ), 'Magento_Validator_Exception', 'Read-only property cannot be changed.'),
            'Read-only created_at' => array(array(
                'created_at' => 1
            ), 'Magento_Validator_Exception', 'Read-only property cannot be changed.')
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage The customer with the specified ID not found.
     */
    public function testUpdateInvalidCustomerId()
    {
        $this->_model->update(1, array('firstname' => 'Foo'));
    }

    /**
     * @param array $customerData
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @dataProvider initAutoGeneratePasswordDataProvider
     */
    public function testAutoGeneratePassword($customerData)
    {
        $expected = new Mage_Customer_Model_Customer();
        $expected->load(1);

        $this->_updateAndCheckCustomer($expected->getId(),
            $customerData, 'assertEquals', array('autogenerate_password'));

        $actual = new Mage_Customer_Model_Customer();
        $actual->load(1);

        $this->assertNotEquals($expected->getPasswordHash(), $actual->getPasswordHash());
    }

    /**
     * @return array
     */
    public function initAutoGeneratePasswordDataProvider()
    {
        return array(
            'Auto generate password' => array(array(
                'autogenerate_password' => true,
            )),
        );
    }
}

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
     * @param $customerData
     */
    protected function _createAndCheckCustomer($customerData)
    {
        $this->_createdCustomer = $this->_model->create($customerData);
        $this->assertInstanceOf('Mage_Customer_Model_Customer', $this->_createdCustomer);
        $this->assertFalse($this->_createdCustomer->isObjectNew());
        $this->assertNotEmpty($this->_createdCustomer->getId());

        foreach ($customerData as $key => $val) {
            $this->assertEquals($val, $this->_createdCustomer->getData($key));
        }
    }

    /**
     * Update and check customer
     *
     * @param $customerId
     * @param $customerData
     * @param $assertFunction
     * @param $forbiddenFields
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
    public function testCreate($customerData, $exceptionName, $exceptionText = '')
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
            ), 'Mage_Core_Exception'),
            'Invalid email' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => 'SomeName',
                'lastname' => 'SomeSurname',
                'suffix' => null,
                'email' => '111@111',
                'password' => '123123q',
            ), 'Mage_Core_Exception'),
            'Invalid password' => array(array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => 'SomeName',
                'lastname' => 'SomeSurname',
                'suffix' => null,
                'email' => 'test' . mt_rand(1000, 9999) . '@mail.com',
                'password' => '123',
            ), 'Mage_Eav_Model_Entity_Attribute_Exception', 'The password must have at least 6 characters.'),
        );
    }

    /**
     * @param array $customerData
     * @param string $exceptionName
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @dataProvider initUpdateCustomerDataProvider
     */
    public function testUpdate($customerData, $exceptionName = '')
    {
        $expected = new Mage_Customer_Model_Customer();
        $expected->load(1);

        if (!empty($exceptionName)) {
            $this->setExpectedException($exceptionName);
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
            ), 'Mage_Core_Exception'),
            'Invalid email' => array(array(
                'email' => '3434@23434'
            ), 'Mage_Core_Exception'),
        );
    }

    /**
     * @param array $customerData
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @dataProvider initForbiddenFieldsUpdateDataProvider
     */
    public function testForbiddenFieldsUpdate($customerData)
    {
        $expected = new Mage_Customer_Model_Customer();
        $expected->load(1);

        $this->_updateAndCheckCustomer($expected->getId(),
            $customerData, 'assertNotEquals');
    }

    /**
     * @return array
     */
    public function initForbiddenFieldsUpdateDataProvider()
    {
        return array(
            'Fields must not be changed' => array(array(
                'website_id' => 111,
                'entity_type_id' => 555,
            )),
        );
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testDelete()
    {
        $previousStoreId = Mage::app()->getStore();
        Mage::app()->setCurrentStore(Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID));

        $this->_model->delete(1);

        Mage::app()->setCurrentStore($previousStoreId);
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

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @expectedException Mage_Core_Exception
     */
    public function testLoadCustomerByIdException()
    {
        $this->_model->delete(100);
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testGetList()
    {
        $expected = new Mage_Customer_Model_Customer();
        $expected->load(1);
        $actual = $this->_model->getList(array(
            'page' => 1,
            'limit' => 1,
            'order' => 'entity_id',
            'dir' => 'asc',
            'filter' => array(
                array(
                    'attribute' => 'entity_id',
                    'eq' => 1
                )
            )
        ), '*');

        $this->assertInternalType('array', $actual);
        $this->assertCount(1, $actual);
        $this->assertEquals($expected->toArray(), $actual[1]->toArray());
    }
}

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

class Mage_Catalog_Service_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Service_Customer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Customer_Service_Customer();
    }

    protected function tearDown()
    {
        Mage::app()->setCurrentStore(Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID));
        if ($this->_model && $this->_model->getCustomer()->getId() > 0) {
            $this->_model->delete($this->_model->getCustomer()->getId());
        }
        $this->_model = null;
    }

    /**
     * @param array $customerData
     * @dataProvider initCreateCustomerDataProvider
     */
    public function testCreate($customerData)
    {
        $customer = $this->_model->create($customerData);
        $this->assertInstanceOf('Mage_Customer_Model_Customer', $customer);
        $this->assertGreaterThan(1, $customer->getId());
    }

    /**
     * @param array $customerData
     * @dataProvider initUpdateCustomerDataProvider
     */
    public function testUpdate($customerData)
    {
        $customerInitData = $this->initCreateCustomerDataProvider();
        $customer = $this->_model->create($customerInitData[0][0]);

        $this->assertInstanceOf('Mage_Customer_Model_Customer', $customer);
        $this->assertGreaterThan(1, $customer->getId());

        $updatedCustomer = $this->_model->update($customer->getId(), $customerData);

        $this->assertInstanceOf('Mage_Customer_Model_Customer', $updatedCustomer);
        $this->assertGreaterThan(1, $updatedCustomer->getId());
        $this->assertEquals($customerData['account']['password'], $updatedCustomer->getPassword());
    }

    /**
     * @return array
     */
    public function initCreateCustomerDataProvider()
    {
        return array(
            array(array('account' => array('website_id' => 0,
                'group_id' => 1,
                'disable_auto_group_change' => 0,
                'prefix' => null,
                'firstname' => 'Alexander',
                'middlename' => null,
                'lastname' => 'Makeev',
                'suffix' => null,
                'email' => 'test' . mt_rand(1000, 9999) . '@mail.com',
                'dob' => null,
                'taxvat' => null,
                'gender' => 1,
                'password' => '123123q',
                'new_password' => null,
                'default_billing' => null,
                'default_shipping' => null,
                'confirmation' => '123123q'
            ))),
        );
    }

    /**
     * @return array
     */
    public function initUpdateCustomerDataProvider()
    {
        return array(
            array(array('account' => array(
                'new_password' => '111',
                'confirmation' => '222'
            ))),
        );
    }
}

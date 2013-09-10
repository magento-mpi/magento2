<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Controller_CustomerTest extends Magento_Backend_Utility_Controller
{
    /**
     * Base controller URL
     *
     * @var string
     */
    protected $_baseControllerUrl;

    public function setUp()
    {
        parent::setUp();
        $this->_baseControllerUrl = 'http://localhost/index.php/backend/admin/customer/';
    }

    public function tearDown()
    {
        /**
         * Unset customer data
         */
        Mage::getSingleton('Magento_Backend_Model_Session')->setCustomerData(null);

        /**
         * Unset messages
         */
        Mage::getSingleton('Magento_Backend_Model_Session')->getMessages(true);
    }


    public function testSaveActionWithEmptyPostData()
    {
        $this->getRequest()->setPost(array());
        $this->dispatch('backend/admin/customer/save');
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl));
    }

    public function testSaveActionWithInvalidFormData()
    {
        $post = array(
            'account' => array(
                'middlename' => 'test middlename',
                'group_id' => 1
            )
        );
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin/customer/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), Magento_Core_Model_Message::ERROR);
        /**
         * Check that customer data were set to session
         */
        $this->assertEquals($post, Mage::getSingleton('Magento_Backend_Model_Session')->getCustomerData());
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'new'));
    }

    public function testSaveActionWithInvalidCustomerAddressData()
    {
        $post = array(
            'account' => array(
                'middlename' => 'test middlename',
                'group_id' => 1,
                'website_id' => 0,
                'firstname' => 'test firstname',
                'lastname' => 'test lastname',
                'email' => 'exmaple@domain.com',
                'default_billing' => '_item1',
            ),
            'address' => array('_item1' => array()),
        );
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin/customer/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), Magento_Core_Model_Message::ERROR);
        /**
         * Check that customer data were set to session
         */
        $this->assertEquals($post, Mage::getSingleton('Magento_Backend_Model_Session')->getCustomerData());
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'new'));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionWithValidCustomerDataAndValidAddressData()
    {
        $post = array(
            'account' => array(
                'middlename' => 'test middlename',
                'group_id' => 1,
                'website_id' => 0,
                'firstname' => 'test firstname',
                'lastname' => 'test lastname',
                'email' => 'exmaple@domain.com',
                'default_billing' => '_item1',
                'password' => 'auto'
            ),
            'address' => array('_item1' => array(
                'firstname' => 'test firstname',
                'lastname' => 'test lastname',
                'street' => array(
                    'test street'
                ),
                'city' => 'test city',
                'country_id' => 'US',
                'postcode' => '01001',
                'telephone' => '+7000000001',
            )),
        );
        $this->getRequest()->setPost($post);
        $this->getRequest()->setParam('back', '1');
        $this->dispatch('backend/admin/customer/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), Magento_Core_Model_Message::ERROR);
        /**
         * Check that customer data were set to session
         */
        $this->assertEmpty(Mage::getSingleton('Magento_Backend_Model_Session')->getCustomerData());

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), Magento_Core_Model_Message::SUCCESS);

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /**
         * Check that customer id set and addresses saved
         */
        $customer = $objectManager->get('Magento_Core_Model_Registry')->registry('current_customer');
        $this->assertInstanceOf('Magento_Customer_Model_Customer', $customer);
        $this->assertCount(1, $customer->getAddressesCollection());

        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl
            . 'edit/id/' . $customer->getId() . '/back/1')
        );
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/customer_sample.php
     */
    public function testSaveActionExistingCustomerAndExistingAddressData()
    {
        $post = array(
            'customer_id' => '1',
            'account' => array(
                'middlename' => 'test middlename',
                'group_id' => 1,
                'website_id' => 1,
                'firstname' => 'test firstname',
                'lastname' => 'test lastname',
                'email' => 'exmaple@domain.com',
                'default_shipping' => '_item1',
                'new_password' => 'auto',
                'sendemail_store_id' => '1',
                'sendemail' => '1',

            ),
            'address' => array(
                '1' => array(
                    'firstname' => 'update firstname',
                    'lastname' => 'update lastname',
                    'street' => array('update street'),
                    'city' => 'update city',
                    'country_id' => 'US',
                    'postcode' => '01001',
                    'telephone' => '+7000000001',
                ),
                '_item1' => array(
                    'firstname' => 'test firstname',
                    'lastname' => 'test lastname',
                    'street' => array('test street'),
                    'city' => 'test city',
                    'country_id' => 'US',
                    'postcode' => '01001',
                    'telephone' => '+7000000001',
                ),
                '_template_' => array(
                    'firstname' => '',
                    'lastname' => '',
                    'street' => array(),
                    'city' => '',
                    'country_id' => 'US',
                    'postcode' => '',
                    'telephone' => '',
                )
            ),
        );
        $this->getRequest()->setPost($post);
        $this->getRequest()->setParam('customer_id', 1);
        $this->dispatch('backend/admin/customer/save');
        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('You saved the customer.')), Magento_Core_Model_Message::SUCCESS
        );

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /**
         * Check that customer id set and addresses saved
         */
        $customer = $objectManager->get('Magento_Core_Model_Registry')->registry('current_customer');
        $this->assertInstanceOf('Magento_Customer_Model_Customer', $customer);

        /**
         * Addresses should be removed by Magento_Customer_Model_Resource_Customer::_saveAddresses during _afterSave
         * addressOne - updated
         * addressTwo - removed
         * addressThree - removed
         * _item1 - new address
         */
        $this->assertCount(2, $customer->getAddressesCollection());

        /** @var $savedCustomer Magento_Customer_Model_Customer */
        $savedCustomer = Mage::getModel('Magento_Customer_Model_Customer');
        $savedCustomer->load($customer->getId());
        /**
         * addressOne - updated
         * _item1 - new address
         */
        $this->assertCount(2, $savedCustomer->getAddressesCollection());

        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'index/key/'));
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/customer_sample.php
     */
    public function testSaveActionCoreException()
    {
        $post = array(
            'account' => array(
                'middlename' => 'test middlename',
                'group_id' => 1,
                'website_id' => 1,
                'firstname' => 'test firstname',
                'lastname' => 'test lastname',
                'email' => 'exmaple@domain.com',
                'password' => 'auto',
            ),
        );
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin/customer/save');
        /*
        * Check that error message is set
        */
        $this->assertSessionMessages(
            $this->equalTo(array('Customer with the same email already exists.')),
            Magento_Core_Model_Message::ERROR
        );
        $this->assertEquals($post, Mage::getSingleton('Magento_Backend_Model_Session')->getCustomerData());
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'new/key/'));
    }
}

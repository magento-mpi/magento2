<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Base controller URL
     *
     * @var string
     */
    protected $_baseControllerUrl;

    protected function setUp()
    {
        parent::setUp();
        $this->_baseControllerUrl = 'http://localhost/index.php/backend/customer/index/';
    }

    protected function tearDown()
    {
        /**
         * Unset customer data
         */
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
            ->setCustomerData(null);

        /**
         * Unset messages
         */
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
            ->getMessages(true);
    }

    public function testSaveActionWithEmptyPostData()
    {
        $this->getRequest()->setPost(array());
        $this->dispatch('backend/customer/index/save');
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
        $this->dispatch('backend/customer/index/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages(
            $this->logicalNot($this->isEmpty()),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
        /**
         * Check that customer data were set to session
         */
        $this->assertEquals(
            $post,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Backend\Model\Session')->getCustomerData()
        );
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'new'));
    }

    /**
     * @magentoDbIsolation enabled
     */
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
        $this->dispatch('backend/customer/index/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages(
            $this->logicalNot($this->isEmpty()),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
        /**
         * Check that customer data were set to session
         */
        $this->assertEquals(
            $post,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Backend\Model\Session')->getCustomerData()
        );
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'new'));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionWithValidCustomerDataAndValidAddressData()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

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
            'address' => array(
                '_item1' => array(
                    'firstname' => 'test firstname',
                    'lastname' => 'test lastname',
                    'street' => array(
                        'test street'
                    ),
                    'city' => 'test city',
                    'country_id' => 'US',
                    'postcode' => '01001',
                    'telephone' => '+7000000001',
                )
            ),
        );
        $this->getRequest()->setPost($post);
        $this->getRequest()->setParam('back', '1');
        $this->dispatch('backend/customer/index/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Message\MessageInterface::TYPE_ERROR);
        /**
         * Check that customer data were set to session
         */
        $this->assertEmpty($objectManager->get('Magento\Backend\Model\Session')->getCustomerData());

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->logicalNot($this->isEmpty()),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );

        /**
         * Check that customer id set and addresses saved
         */
        $registry = $objectManager->get('Magento\Core\Model\Registry');
        $customer = $registry->registry('current_customer');
        $this->assertInstanceOf('Magento\Customer\Model\Customer', $customer);
        $this->assertCount(1, $customer->getAddressesCollection());

        $this->assertRedirect(
            $this->stringStartsWith(
                $this->_baseControllerUrl
                . 'edit/id/' . $customer->getId() . '/back/1'
            )
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
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
        $this->dispatch('backend/customer/index/save');
        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('You saved the customer.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /**
         * Check that customer id set and addresses saved
         */
        $customer = $objectManager->get('Magento\Core\Model\Registry')->registry('current_customer');
        $this->assertInstanceOf('Magento\Customer\Model\Customer', $customer);

        /**
         * Addresses should be removed by \Magento\Customer\Model\Resource\Customer::_saveAddresses during _afterSave
         * addressOne - updated
         * addressTwo - removed
         * addressThree - removed
         * _item1 - new address
         */
        $this->assertCount(2, $customer->getAddressesCollection());

        /** @var $savedCustomer \Magento\Customer\Model\Customer */
        $savedCustomer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Customer');
        $savedCustomer->load($customer->getId());
        /**
         * addressOne - updated
         * _item1 - new address
         */
        $this->assertCount(2, $savedCustomer->getAddressesCollection());

        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'index/key/'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
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
        $this->dispatch('backend/customer/index/save');
        /*
        * Check that error message is set
        */
        $this->assertSessionMessages(
            $this->equalTo(array('Customer with the same email already exists in associated website.')),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
        $this->assertEquals(
            $post,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Backend\Model\Session')->getCustomerData()
        );
        $this->assertRedirect($this->stringStartsWith($this->_baseControllerUrl . 'new/key/'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testEditAction()
    {
        $customerData = [
            'customer_id' => '1',
            'account' => [
                'middlename' => 'new middlename',
                'group_id' => 1,
                'website_id' => 1,
                'firstname' => 'new firstname',
                'lastname' => 'new lastname',
                'email' => 'exmaple@domain.com',
                'default_shipping' => '_item1',
                'new_password' => 'auto',
                'sendemail_store_id' => '1',
                'sendemail' => '1',

            ],
            'address' => [
                '1' => array(
                    'firstname' => 'update firstname',
                    'lastname' => 'update lastname',
                    'street' => array('update street'),
                    'city' => 'update city',
                    'country_id' => 'US',
                    'postcode' => '01001',
                    'telephone' => '+7000000001',
                ),
                '_item1' => [
                    'firstname' => 'default firstname',
                    'lastname' => 'default lastname',
                    'street' => array('default street'),
                    'city' => 'default city',
                    'country_id' => 'US',
                    'postcode' => '01001',
                    'telephone' => '+7000000001',
                ],
                '_template_' => [
                    'firstname' => '',
                    'lastname' => '',
                    'street' => array(),
                    'city' => '',
                    'country_id' => 'US',
                    'postcode' => '',
                    'telephone' => '',
                ]
            ]
        ];
        /**
         * set customer data
         */
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
            ->setCustomerData($customerData);
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/customer/index/edit');
        $body = $this->getResponse()->getBody();

        // verify
        $this->assertContains('<h1 class="title">new firstname new lastname</h1>', $body);

        $accountStr = 'data-ui-id="adminhtml-edit-tab-account-fieldset-element-text-account-';
        $this->assertNotContains($accountStr . 'firstname"  value="test firstname"', $body);
        $this->assertContains($accountStr . 'firstname"  value="new firstname"', $body);

        $addressStr = 'data-ui-id="adminhtml-edit-tab-addresses-fieldset-element-text-address-';
        $this->assertNotContains($addressStr . '1-firstname"  value="test firstname"', $body);
        $this->assertContains($addressStr . '1-firstname"  value="update firstname"', $body);
        $this->assertContains($addressStr . '2-firstname"  value="test firstname"', $body);
        $this->assertContains($addressStr . '3-firstname"  value="removed firstname"', $body);
        $this->assertContains($addressStr . 'item1-firstname"  value="default firstname"', $body);
        $this->assertContains($addressStr . 'template-firstname"  value=""', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testEditActionNoSessionData()
    {
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/customer/index/edit');
        $body = $this->getResponse()->getBody();

        // verify
        $this->assertContains('<h1 class="title">test firstname test lastname</h1>', $body);

        $accountStr = 'data-ui-id="adminhtml-edit-tab-account-fieldset-element-text-account-';
        $this->assertContains($accountStr . 'firstname"  value="test firstname"', $body);

        $addressStr = 'data-ui-id="adminhtml-edit-tab-addresses-fieldset-element-text-address-';
        $this->assertContains($addressStr . '1-firstname"  value="test firstname"', $body);
        $this->assertContains($addressStr . '2-firstname"  value="test firstname"', $body);
        $this->assertContains($addressStr . '3-firstname"  value="removed firstname"', $body);
        $this->assertNotContains($addressStr . 'item1-firstname"', $body);
        $this->assertContains($addressStr . 'template-firstname"  value=""', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testNewAction()
    {
        $this->dispatch('backend/customer/index/edit');
        $body = $this->getResponse()->getBody();

        // verify
        $this->assertContains('<h1 class="title">New Customer</h1>', $body);

        $accountStr = 'data-ui-id="adminhtml-edit-tab-account-fieldset-element-text-account-';
        $this->assertContains($accountStr . 'firstname"  value=""', $body);

        $addressStr = 'data-ui-id="adminhtml-edit-tab-addresses-fieldset-element-text-address-';
        $this->assertNotContains($addressStr . '1-firstname"', $body);
        $this->assertNotContains($addressStr . '2-firstname"', $body);
        $this->assertNotContains($addressStr . '3-firstname"', $body);
        $this->assertNotContains($addressStr . 'item1-firstname"', $body);
        $this->assertContains($addressStr . 'template-firstname"  value=""', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testDeleteAction()
    {
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/customer/index/delete');
        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['You deleted the customer.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testNotExistingCustomerDeleteAction()
    {
        $this->getRequest()->setParam('id', 2);
        $this->dispatch('backend/customer/index/delete');
        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 2']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testCartAction()
    {
        $this->getRequest()->setParam('id', 1)
            ->setParam('website_id', 1)
            ->setPost('delete', 1);
        $this->dispatch('backend/customer/index/cart');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<div id="customer_cart_grid1">', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testProductReviewsAction()
    {
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/customer/index/productReviews');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<div id="reviwGrid">', $body);
    }
}

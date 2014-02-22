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
use Magento\Customer\Controller\RegistryConstants;
use Magento\Newsletter\Model\Subscriber;
use Magento\TestFramework\Helper\Bootstrap;

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

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $customerService;

    protected function setUp()
    {
        parent::setUp();
        $this->_baseControllerUrl = 'http://localhost/index.php/backend/customer/index/';
        $this->customerService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerServiceInterface');
    }

    protected function tearDown()
    {
        /**
         * Unset customer data
         */
        Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
            ->setCustomerData(null);

        /**
         * Unset messages
         */
        Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
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
            Bootstrap::getObjectManager()
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
            Bootstrap::getObjectManager()
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
        $objectManager = Bootstrap::getObjectManager();

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
        $customer = $registry->registry(RegistryConstants::CURRENT_CUSTOMER);
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
        $objectManager = Bootstrap::getObjectManager();

        /**
         * Check that customer id set and addresses saved
         */
        $customer = $objectManager->get('Magento\Core\Model\Registry')->registry(RegistryConstants::CURRENT_CUSTOMER);
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
        $savedCustomer = Bootstrap::getObjectManager()
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
            Bootstrap::getObjectManager()
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
        Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
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

    /**
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testMassSubscriberAction()
    {
        // Pre-condition
        /** @var \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory */
        $subscriberFactory = Bootstrap::getObjectManager()->get('Magento\Newsletter\Model\SubscriberFactory');
        $this->assertEquals(null, $subscriberFactory->create()->loadByCustomer(1)->getSubscriberStatus());
        $this->assertEquals(null, $subscriberFactory->create()->loadByCustomer(2)->getSubscriberStatus());
        // Setup
        $this->getRequest()->setParam('customer', [1, 2]);

        // Test
        $this->dispatch('backend/customer/index/massSubscribe');

        // Assertions
        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['A total of 2 record(s) were updated.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertEquals(Subscriber::STATUS_SUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(1)->getSubscriberStatus());
        $this->assertEquals(Subscriber::STATUS_SUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(2)->getSubscriberStatus());
    }

    public function testMassSubscriberActionNoSelection()
    {
        $this->dispatch('backend/customer/index/massSubscribe');

        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['Please select customer(s).']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    public function testMassSubscriberActionInvalidId()
    {
        $this->getRequest()->setParam('customer', [4200]);

        $this->dispatch('backend/customer/index/massSubscribe');

        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 4200']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testMassSubscriberActionPartialUpdate()
    {
        // Pre-condition
        /** @var \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory */
        $subscriberFactory = Bootstrap::getObjectManager()->get('Magento\Newsletter\Model\SubscriberFactory');
        $this->assertEquals(null, $subscriberFactory->create()->loadByCustomer(1)->getSubscriberStatus());
        $this->assertEquals(null, $subscriberFactory->create()->loadByCustomer(2)->getSubscriberStatus());
        // Setup
        $this->getRequest()->setParam('customer', [1, 4200, 2]);

        // Test
        $this->dispatch('backend/customer/index/massSubscribe');

        // Assertions
        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['A total of 2 record(s) were updated.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 4200']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
        $this->assertEquals(Subscriber::STATUS_SUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(1)->getSubscriberStatus());
        $this->assertEquals(Subscriber::STATUS_SUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(2)->getSubscriberStatus());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testMassDeleteAction()
    {
        $this->getRequest()->setPost('customer', [1]);
        $this->dispatch('backend/customer/index/massDelete');
        $this->assertSessionMessages(
            $this->equalTo(['A total of 1 record(s) were deleted.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('customer/index'));
    }

    public function testInvalidIdMassDeleteAction()
    {
        $this->getRequest()->setPost('customer', [1]);
        $this->dispatch('backend/customer/index/massDelete');
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 1']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testMassAssignGroupAction()
    {
        $customer = $this->customerService->getCustomer(1);
        $this->assertEquals(1, $customer->getGroupId());

        $this->getRequest()->setParam('group', 0)->setPost('customer', [1]);
        $this->dispatch('backend/customer/index/massAssignGroup');
        $this->assertSessionMessages(
            $this->equalTo(['A total of 1 record(s) were updated.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('customer/index'));

        $customer = $this->customerService->getCustomer(1);
        $this->assertEquals(0, $customer->getGroupId());
    }

    /**
     * Valid group Id but no data fixture so no customer exists with customer Id = 1
     */
    public function testMassAssignGroupActionInvalidCustomerId()
    {
        $this->getRequest()->setParam('group', 0)->setPost('customer', [1]);
        $this->dispatch('backend/customer/index/massAssignGroup');
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 1']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * Valid group Id but no customer Ids specified
     */
    public function testMassAssignGroupActionNoCustomerIds()
    {
        $this->getRequest()->setParam('group', 0);
        $this->dispatch('backend/customer/index/massAssignGroup');
        $this->assertSessionMessages(
            $this->equalTo(['Please select customer(s).']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testMassAssignGroupActionPartialUpdate()
    {
        $this->assertEquals(1, $this->customerService->getCustomer(1)->getGroupId());
        $this->assertEquals(1, $this->customerService->getCustomer(2)->getGroupId());

        $this->getRequest()->setParam('group', 0)->setPost('customer', [1, 4200, 2]);
        $this->dispatch('backend/customer/index/massAssignGroup');
        $this->assertSessionMessages(
            $this->equalTo(['A total of 2 record(s) were updated.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 4200']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );

        $this->assertEquals(0, $this->customerService->getCustomer(1)->getGroupId());
        $this->assertEquals(0, $this->customerService->getCustomer(2)->getGroupId());
    }



    /**
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testMassUnsubscriberAction()
    {
        // Setup
        /** @var \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory */
        $subscriberFactory = Bootstrap::getObjectManager()->get('Magento\Newsletter\Model\SubscriberFactory');
        $subscriberFactory->create()->updateSubscription(1, true);
        $subscriberFactory->create()->updateSubscription(2, true);
        $this->getRequest()->setParam('customer', [1, 2]);

        // Test
        $this->dispatch('backend/customer/index/massUnsubscribe');

        // Assertions
        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['A total of 2 record(s) were updated.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertEquals(Subscriber::STATUS_UNSUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(1)->getSubscriberStatus());
        $this->assertEquals(Subscriber::STATUS_UNSUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(2)->getSubscriberStatus());
    }

    public function testMassUnsubscriberActionNoSelection()
    {
        $this->dispatch('backend/customer/index/massUnsubscribe');

        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['Please select customer(s).']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    public function testMassUnsubscriberActionInvalidId()
    {
        $this->getRequest()->setParam('customer', [4200]);

        $this->dispatch('backend/customer/index/massUnsubscribe');

        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 4200']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testMassUnsubscriberActionPartialUpdate()
    {
        // Setup
        /** @var \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory */
        $subscriberFactory = Bootstrap::getObjectManager()->get('Magento\Newsletter\Model\SubscriberFactory');
        $subscriberFactory->create()->updateSubscription(1, true);
        $subscriberFactory->create()->updateSubscription(2, true);
        $this->getRequest()->setParam('customer', [1, 4200, 2]);

        // Test
        $this->dispatch('backend/customer/index/massUnsubscribe');

        // Assertions
        $this->assertRedirect($this->stringContains('customer/index'));
        $this->assertSessionMessages(
            $this->equalTo(['A total of 2 record(s) were updated.']),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with customerId = 4200']),
            \Magento\Message\MessageInterface::TYPE_ERROR
        );
        $this->assertEquals(Subscriber::STATUS_UNSUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(1)->getSubscriberStatus());
        $this->assertEquals(Subscriber::STATUS_UNSUBSCRIBED,
            $subscriberFactory->create()->loadByCustomer(2)->getSubscriberStatus());
    }
}

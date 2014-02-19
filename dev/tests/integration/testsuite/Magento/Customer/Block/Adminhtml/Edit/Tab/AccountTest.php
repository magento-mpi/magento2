<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

/**
 * Test for Account
 *
 * @magentoAppArea adminhtml
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{
    /** @var Account */
    protected $accountBlock;

    /** @var \Magento\ObjectManager */
    protected $objectManager;

    /** @var \Magento\Core\Model\Registry */
    protected $coreRegistry;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Backend\Model\Session */
    protected $backendSession;

    /** @var  \Magento\Backend\Block\Template\Context */
    protected $context;

    /** @var Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $customerService;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->coreRegistry = $this->objectManager->get('Magento\Core\Model\Registry');
        $this->coreRegistry->register('current_customer_id', 1);

        $this->storeManager = $this->objectManager->get('Magento\Core\Model\StoreManager');
        $this->backendSession = $this->objectManager->get('Magento\Backend\Model\Session');

        $this->context = $this->objectManager
            ->get(
                'Magento\Backend\Block\Template\Context',
                ['storeManager' => $this->storeManager, 'backendSession' => $this->backendSession]
            );

        $this->accountBlock = $this->objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tab\Account',
                '',
                ['context' => $this->context]
            );

        $websiteId = $this->objectManager
            ->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()
            ->getWebsiteId();
        $customer = $this->objectManager
            ->create('Magento\Customer\Model\Customer')
            ->setWebsiteId($websiteId)
            ->loadByEmail('customer@example.com');

        $this->coreRegistry->register('current_customer', $customer);

        /** @var Magento\Customer\Service\V1\CustomerServiceInterface $customerService */
        $this->customerService = $this->objectManager->get('Magento\Customer\Service\V1\CustomerServiceInterface');
    }

    public function tearDown()
    {
        $this->coreRegistry->unregister('current_customer_id');
        $this->coreRegistry->unregister('current_customer');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->backendSession->setCustomerData(
            ['customer_id' => 1, 'account' => $this->customerService->getCustomer(1)->__toArray()]
        );

        $result = $this->accountBlock->initForm()->toHtml();

        // Verify account email
        $this->assertRegExp('/id="_accountemail"[^>]*value="customer@example.com"/', $result);
        $this->assertRegExp('/input id="_accountfirstname"[^>]*value="Firstname"/', $result);

        // Verify confirmation controls are not present
        $this->assertNotContains('field-confirmation', $result);
        $this->assertNotContains('_accountconfirmation', $result);

        // Prefix is present but empty
        $this->assertRegExp('/<input id="_accountprefix"[^>]*value=""/', $result);

        // Does not contain send email controls
        $this->assertNotContains('<input id="_accountsendemail"', $result);
        $this->assertNotContains('<select id="_accountsendemail_store_id"', $result);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testNeedsConfirmation()
    {
        $this->backendSession->setCustomerData(
            ['customer_id' => 1, 'account' => $this->customerService->getCustomer(1)->__toArray()]
        );

        $result = $this->accountBlock->initForm()->toHtml();

        // Verify confirmation controls are present
        $this->assertContains('<div class="field field-confirmation "', $result);
        $this->assertContains('<select id="_accountconfirmation"', $result);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testPrefix()
    {
        $this->backendSession->setCustomerData(
            [
                'customer_id' => 1,
                'account' => array_merge($this->customerService->getCustomer(1)->__toArray(), ['prefix' => 'Mr']),
            ]
        );
        $result = $this->accountBlock->initForm()->toHtml();

        // Prefix has value
        $this->assertRegExp('/<input id="_accountprefix"[^>]*value="Mr"/', $result);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testNotReadOnly()
    {
        $this->backendSession->setCustomerData(
            [
                'customer_id' => 1,
                'account' => $this->customerService->getCustomer(1)->__toArray(),
            ]
        );
        $result = $this->accountBlock->initForm()->toHtml();
        $element = $this->accountBlock->getForm()->getElement('firstname');

        // Make sure readonly has not been set (is null) or set to false
        $this->assertTrue(is_null($element->getReadonly()) || !$element->getReadonly());
    }


    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testNewCustomer()
    {
        $customerBuilder = new \Magento\Customer\Service\V1\Dto\CustomerBuilder();

        $this->backendSession->setCustomerData(
            [
                'customer_id' => 0,
                'account' => $customerBuilder->create()->__toArray(),
            ]
        );
        $result = $this->accountBlock->initForm()->toHtml();

        // Contains send email controls
        $this->assertContains('<input id="_accountsendemail"', $result);
        $this->assertContains('<select id="_accountsendemail_store_id"', $result);
    }
}
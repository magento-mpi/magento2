<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Controller;

/**
 * @magentoDbIsolation enabled
 */
class ManageTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Core\Model\Session
     */
    protected $coreSession;

    protected function setUp()
    {
        parent::setUp();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $this->customerSession->setCustomerId(1);
        $this->coreSession = $objectManager->get('Magento\Core\Model\Session');
        $this->coreSession->setData('_form_key', 'formKey');
    }

    protected function tearDown()
    {
        $this->customerSession->setCustomerId(null);
        $this->coreSession->unsData('_form_key');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveAction()
    {
        $this->getRequest()
            ->setParam('form_key', 'formKey')
            ->setParam('is_subscribed', '1');
        $this->dispatch('newsletter/manage/save');

        $this->assertRedirect($this->stringContains('customer/account/'));

        /**
         * Check that errors
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Message\MessageInterface::TYPE_ERROR);

        /**
         * Check that success message
         */
        $this->assertSessionMessages(
            $this->equalTo(array('We saved the subscription.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveActionRemoveSubscription()
    {
        $this->getRequest()
            ->setParam('form_key', 'formKey')
            ->setParam('is_subscribed', '0');
        $this->dispatch('newsletter/manage/save');

        $this->assertRedirect($this->stringContains('customer/account/'));

        /**
         * Check that errors
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Message\MessageInterface::TYPE_ERROR);

        /**
         * Check that success message
         */
        $this->assertSessionMessages(
            $this->equalTo(array('We removed the subscription.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}

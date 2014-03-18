<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
namespace Magento\Newsletter\Controller;

use Magento\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractController;

class SubscriberTest extends AbstractController
{
    public function testNewAction()
    {
        $this->getRequest()->setMethod('POST');

        $this->dispatch('newsletter/subscriber/new');

        $this->assertSessionMessages($this->isEmpty());
        $this->assertRedirect($this->anything());
    }

    public function testNewActionUnusedEmail()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost([
            'email' => 'not_used@example.com'
        ]);

        $this->dispatch('newsletter/subscriber/new');

        $this->assertSessionMessages($this->equalTo(['Thank you for your subscription.']));
        $this->assertRedirect($this->anything());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testNewActionUsedEmail()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost([
            'email' => 'customer@example.com'
        ]);

        $this->dispatch('newsletter/subscriber/new');

        $this->assertSessionMessages($this->equalTo([
                'There was a problem with the subscription: This email address is already assigned to another user.'
            ]));
        $this->assertRedirect($this->anything());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testNewActionOwnerEmail()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost([
            'email' => 'customer@example.com'
        ]);
        $this->login(1);

        $this->dispatch('newsletter/subscriber/new');

        $this->assertSessionMessages($this->equalTo(['Thank you for your subscription.']));
        $this->assertRedirect($this->anything());
    }

    /**
     * Login the user
     *
     * @param string $customerId Customer to mark as logged in for the session
     * @return void
     */
    protected function login($customerId)
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Customer\Model\Session');
        $session->loginById($customerId);
    }
}

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

namespace Magento\Customer\Controller;

class AccountTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $session->login('customer@example.com', 'password');
        $this->dispatch('customer/account/index');
        $this->assertContains('<div class="my-account">', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCreatepasswordAction()
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Customer')->load(1);

        $token = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Helper\Data')
            ->generateResetPasswordLinkToken();
        $customer->changeResetPasswordLinkToken($token);

        $this->getRequest()->setParam('token', $token);
        $this->getRequest()->setParam('id', $customer->getId());

        $this->dispatch('customer/account/createpassword');
        $text = $this->getResponse()->getBody();
        $this->assertTrue((bool)preg_match('/' . $token . '/m', $text));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testOpenActionCreatepasswordAction()
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Customer')->load(1);

        $token = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Helper\Data')
            ->generateResetPasswordLinkToken();
        $customer->changeResetPasswordLinkToken($token);

        $this->getRequest()->setParam('token', $token);
        $this->getRequest()->setParam('id', $customer->getId());

        $this->dispatch('customer/account/createpassword');
        $this->assertNotEmpty($this->getResponse()->getBody());

        $headers = $this->getResponse()->getHeaders();
        $failed = false;
        foreach ($headers as $header) {
            if (preg_match('~customer\/account\/login~', $header['value'])) {
                $failed = true;
                break;
            }
        }
        $this->assertFalse($failed, 'Action is closed');
    }
}

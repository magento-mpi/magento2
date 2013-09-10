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

class Magento_Customer_Controller_AccountTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $session = Mage::getModel('Magento_Customer_Model_Session');
        $session->login('customer@example.com', 'password');
        $this->dispatch('customer/account/index');
        $this->assertContains('<div class="my-account">', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCreatepasswordAction()
    {
        /** @var Magento_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load(1);

        $token = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Customer_Helper_Data')
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
        /** @var Magento_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load(1);

        $token = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Customer_Helper_Data')
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

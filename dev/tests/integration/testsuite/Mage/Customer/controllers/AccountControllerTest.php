<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_AccountControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $session = Mage::getModel('Mage_Customer_Model_Session');
        $session->login('customer@example.com', 'password');
        $this->dispatch('customer/account/index');
        $this->assertContains('<div class="my-account">', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testCreatepasswordAction()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load(1);

        $token = Mage::helper('Mage_Customer_Helper_Data')->generateResetPasswordLinkToken();
        $customer->changeResetPasswordLinkToken($token);

        $this->getRequest()->setParam('token', $token);
        $this->getRequest()->setParam('id', $customer->getId());

        $this->dispatch('customer/account/createpassword');
        $text = $this->getResponse()->getBody();
        $this->assertTrue((bool)preg_match('/' . $token . '/m', $text));
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testOpenActionCreatepasswordAction()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load(1);

        $token = Mage::helper('Mage_Customer_Helper_Data')->generateResetPasswordLinkToken();
        $customer->changeResetPasswordLinkToken($token);

        $this->getRequest()->setParam('token', $token);
        $this->getRequest()->setParam('id', $customer->getId());

        $this->dispatch('customer/account/createpassword');
        $this->assertNotEmpty($this->getResponse()->getBody());

        $headers = $this->getResponse()->getHeaders();
        $failed = false;
        foreach ($headers as $header) {
            if (preg_match('/customer\/account\/login/', $header['value'])) {
                $failed = true;
                break;
            }
        }
        $this->assertFalse($failed, 'Action is closed');
    }
}

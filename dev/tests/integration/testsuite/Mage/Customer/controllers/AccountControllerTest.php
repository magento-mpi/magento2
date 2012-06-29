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
        $session = new Mage_Customer_Model_Session;
        $session->login('customer@example.com', 'password');
        $this->dispatch('customer/account/index');
        $this->assertContains('<div class="my-account">', $this->getResponse()->getBody());
    }
}

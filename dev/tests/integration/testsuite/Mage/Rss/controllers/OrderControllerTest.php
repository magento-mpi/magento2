<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Rss_OrderControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testNewActionAuthorizationFailed()
    {
        $this->dispatch('rss/order/new');
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order_commit_workaround.php
     */
    public function testNewAction()
    {
        $admin = new Mage_User_Model_User;
        $admin->loadByUsername(Magento_Test_Bootstrap::ADMIN_NAME);
        /** @var $session Mage_Backend_Model_Auth_Session */
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $session->setUser($admin)->processLogin();

        $this->dispatch('rss/order/new');
        $this->assertHeaderPcre('Content-Type', '/text\/xml/');
        $this->assertContains('#100000001', $this->getResponse()->getBody());
    }
}

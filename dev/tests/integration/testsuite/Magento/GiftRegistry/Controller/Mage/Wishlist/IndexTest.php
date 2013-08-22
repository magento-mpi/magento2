<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Controller_Magento_Wishlist_IndexTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $this->markTestIncomplete('Bug MAGE-6447');
        $session = Mage::getModel('Magento_Customer_Model_Session');
        $this->assertTrue($session->login('customer@example.com', 'password')); // fixture
        $this->dispatch('wishlist/index/index');
        $this->assertContains('id="giftregistry-form">', $this->getResponse()->getBody());
    }
}

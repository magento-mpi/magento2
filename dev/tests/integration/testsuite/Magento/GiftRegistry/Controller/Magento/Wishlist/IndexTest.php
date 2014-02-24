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

namespace Magento\GiftRegistry\Controller\Magento\Wishlist;

class IndexTest
    extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $this->markTestIncomplete('Bug MAGE-6447');
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountService');
        $customer = $service->authenticate('customer@example.com', 'password');
        $session->setCustomerDtoAsLoggedIn($customer);
        $this->dispatch('wishlist/index/index');
        $this->assertContains('id="giftregistry-form">', $this->getResponse()->getBody());
    }
}

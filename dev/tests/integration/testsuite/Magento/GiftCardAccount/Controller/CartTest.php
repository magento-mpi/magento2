<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Controller;

class CartTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/GiftCardAccount/_files/giftcardaccount.php
     */
    public function testQuickCheckAction()
    {
        $this->getRequest()->setParam('giftcard_code', 'giftcardaccount_fixture');
        $this->dispatch('giftcard/cart/quickCheck');
        $output = $this->getResponse()->getBody();
        $this->assertContains('giftcardaccount_fixture', $output);
        $this->assertContains('$9.99', $output);
    }
}

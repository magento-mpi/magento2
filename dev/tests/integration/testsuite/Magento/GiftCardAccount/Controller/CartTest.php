<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Controller;

class CartTest extends \Magento\TestFramework\TestCase\AbstractController
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

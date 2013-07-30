<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Controller_CartTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Enterprise/GiftCardAccount/_files/giftcardaccount.php
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

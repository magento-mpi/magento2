<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Paypal
 * @magentoDataFixture Mage/Paypal/_files/payflowadvanced/order.php
 */
class Mage_Paypal_PayflowadvancedControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testCancelPaymentActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflowadvanced/cancelpayment');
        $this->assertContains(
            'window_top.checkout.gotoSection("payment");',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'checkout-review-submit\').show();',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'iframe-warning\').hide();',
            $this->getResponse()->getBody()
        );
    }

    public function testReturnurlActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflowadvanced/returnurl');
        $this->assertContains(
            'window_top.checkout.gotoSection("payment");',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'checkout-review-submit\').show();',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'iframe-warning\').hide();',
            $this->getResponse()->getBody()
        );
    }

    public function testFormActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflowadvanced/form');
        $this->assertContains(
            '<form id="token_form" method="POST" action="https://payflowlink.paypal.com/">',
            $this->getResponse()->getBody()
        );
    }
}

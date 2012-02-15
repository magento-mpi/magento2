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
 * @magentoDataFixture Mage/Paypal/_files/hostedpro/order.php
 */
class Mage_Paypal_HostedproControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testCancelActionIsContentGenerated()
    {
        $this->dispatch('paypal/hostedpro/cancel');
        $this->assertContains(
            'window.top.checkout.gotoSection("payment");',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window.top.document.getElementById(\'checkout-review-submit\').show();',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window.top.document.getElementById(\'iframe-warning\').hide();',
            $this->getResponse()->getBody()
        );
    }
}

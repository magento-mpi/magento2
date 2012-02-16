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
 * @magentoDataFixture Mage/Paypal/_files/standard/order.php
 */
class Mage_Paypal_StandardControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testRedirectActionIsContentGenerated()
    {
        $this->dispatch('paypal/standard/redirect');
        $this->assertContains(
            '<form action="https://www.paypal.com/webscr" id="paypal_standard_checkout"'
                . ' name="paypal_standard_checkout" method="POST">',
            $this->getResponse()->getBody()
        );
    }
}

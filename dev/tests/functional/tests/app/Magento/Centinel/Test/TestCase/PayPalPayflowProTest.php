<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\TestCase;

use Mtf\Factory\Factory;

/**
 * Class PayPalPayflowProTest
 * Tests CreditCard validation via Magento one page checkout and 3D Secure payment methods.
 *
 * @package Magento\Centinel
 */
class PayPalPayflowProTest extends Steps
{
    /**
     * Place order on frontend via one page checkout and PayPal PayflowPro 3D Secure payment method
     * with valid credit card.
     *
     * @ZephyrId MAGETWO-12437
     */
    public function testValidCreditCard()
    {
        //Data
        $fixture = Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPayflowProValidCc();
        $fixture->persist();
        //Steps
        $this->_addProducts($fixture);
        $this->_magentoCheckoutProcess($fixture);
        $this->_validateCc($fixture);
        $this->_placeOrder();
        //Verifying
        $this->_verifyOrder($fixture);
    }
}

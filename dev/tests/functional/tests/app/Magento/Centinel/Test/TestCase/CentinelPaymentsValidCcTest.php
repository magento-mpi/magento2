<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CentinelPaymentsValidCcTest
 * Tests CreditCard validation via Magento one page checkout and 3D Secure payment methods.
 *
 */
class CentinelPaymentsValidCcTest extends AbstractCentinelPaymentsTest
{
    /**
     * Place order on frontend via one page checkout and PayPal PayflowPro 3D Secure payment method
     * with valid credit card.
     *
     * @param FixtureInterface $fixture
     * @dataProvider validCreditCardDataProvider
     * @ZephyrId MAGETWO-12437, MAGETWO-12439, MAGETWO-12828
     */
    public function testValidCreditCard(FixtureInterface $fixture)
    {
        //Data
        $fixture->persist();
        $this->clearShoppingCart();
        //Steps
        if ($fixture->getCustomerName()) {
            $this->_createCustomer($fixture);
        }
        $this->_addProducts($fixture);
        $this->_magentoCheckoutProcess($fixture);
        $this->_validateCc($fixture);
        $this->_placeOrder();

        //Verifying
        $this->_verifyOrder($fixture);
    }

    /**
     * @return array
     */
    public function validCreditCardDataProvider()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPaymentsProValidCc()],
            [Factory::getFixtureFactory()->getMagentoCentinelRegisteredAuthorizenetValidCc()],
        ];
    }
}

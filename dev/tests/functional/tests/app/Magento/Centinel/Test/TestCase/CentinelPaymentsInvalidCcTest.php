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
 * Class CentinelPaymentsInvalidCcTest
 * Tests CreditCard validation via Magento one page checkout and 3D Secure payment methods.
 *
 */
class CentinelPaymentsInvalidCcTest extends AbstractCentinelPaymentsTest
{
    /**
     * Try Place order on frontend via one page checkout and PayPal PayflowPro 3D Secure payment method
     * with invalid credit card.
     *
     * @param FixtureInterface $fixture
     * @dataProvider invalidCreditCardDataProvider
     * @ZephyrId MAGETWO-13396, MAGETWO-13398, MAGETWO-13399
     */
    public function testInvalidCreditCard(FixtureInterface $fixture)
    {
        //Data
        $fixture->persist();

        //Steps
        $this->clearShoppingCart();
        $this->_addProducts($fixture);
        $this->_magentoCheckoutProcess($fixture);
        $this->_submitCc($fixture);

        //Verification
        $this->assertContains(
            "Verification Failed\nThe card has failed verification with the issuer bank. Order cannot be placed.",
            $this->_getFailedMessage($fixture),
            'Failed Verification message not found'
        );
    }

    /**
     * @return array
     */
    public function invalidCreditCardDataProvider()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPayflowProInvalidCc()],
            [Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPaymentsProInvalidCc()],
            [Factory::getFixtureFactory()->getMagentoCentinelGuestAuthorizenetInvalidCc()],
        ];
    }
}

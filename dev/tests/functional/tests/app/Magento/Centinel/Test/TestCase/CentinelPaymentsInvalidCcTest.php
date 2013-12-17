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
use Mtf\Fixture\DataFixture;

/**
 * Class CentinelPaymentsInvalidCcTest
 * Tests CreditCard validation via Magento one page checkout and 3D Secure payment methods.
 *
 * @package Magento\Centinel
 */
class CentinelPaymentsInvalidCcTest extends AbstractCentinelPaymentsTest
{
    /**
     * Try Place order on frontend via one page checkout and PayPal PayflowPro 3D Secure payment method
     * with invalid credit card.
     *
     * @param DataFixture $fixture
     * @dataProvider invalidCreditCardDataProvider
     * @ZephyrId MAGETWO-13396, MAGETWO-13398, MAGETWO-13399
     */
    public function testInvalidCreditCard(DataFixture $fixture)
    {
        //Data
        $fixture->persist();

        //Steps
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
        return array(
            array(Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPayflowProInvalidCc()),
            array(Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPaymentsProInvalidCc()),
            array(Factory::getFixtureFactory()->getMagentoCentinelGuestAuthorizenetInvalidCc()),
        );
    }
}

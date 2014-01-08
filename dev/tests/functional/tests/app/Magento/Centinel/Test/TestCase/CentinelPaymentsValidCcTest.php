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
 * Class CentinelPaymentsValidCcTest
 * Tests CreditCard validation via Magento one page checkout and 3D Secure payment methods.
 *
 * @package Magento\Centinel
 */
class CentinelPaymentsValidCcTest extends AbstractCentinelPaymentsTest
{
    /**
     * Place order on frontend via one page checkout and PayPal PayflowPro 3D Secure payment method
     * with valid credit card.
     *
     * @param DataFixture $fixture
     * @dataProvider validCreditCardDataProvider
     * @ZephyrId MAGETWO-12437, MAGETWO-12439, MAGETWO-12828
     */
    public function testValidCreditCard(DataFixture $fixture)
    {
        //Data
        $fixture->persist();

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
        return array(
            array(Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPayflowProValidCc()),
            array(Factory::getFixtureFactory()->getMagentoCentinelGuestPayPalPaymentsProValidCc()),
            array(Factory::getFixtureFactory()->getMagentoCentinelRegisteredAuthorizenetValidCc()),
        );
    }
}

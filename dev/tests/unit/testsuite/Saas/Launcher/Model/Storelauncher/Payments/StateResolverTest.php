<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Payments_StateResolverTest
    extends Saas_Launcher_Model_Tile_ConfigBased_StateResolverTestCaseAbstract
{
    /**
     * @param Magento_Core_Model_App $app
     * @return Saas_Launcher_Model_Storelauncher_Payments_StateResolver
     */
    protected function _getStateResolverInstance(Magento_Core_Model_App $app)
    {
        return new Saas_Launcher_Model_Storelauncher_Payments_StateResolver($app);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function isTileCompleteDataProvider()
    {
        // Payments tile is considered to be complete when at least one of the related payment methods is active
        return array(
            array(
                array(
                    'payment/paypal_express/active' => 1,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 1,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 1,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 1,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 1,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 1,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 1,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                false,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 1,
                    'payment/paypal_standard/active' => 1,
                    'payment/payflow_advanced/active' => 1,
                    'payment/paypal_direct/active' => 1,
                    'payment/payflow_link/active' => 1,
                    'payment/verisign/active' => 1,
                    'payment/authorizenet/active' => 1,
                ),
                true,
            ),
        );
    }
}

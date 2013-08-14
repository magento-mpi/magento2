<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * State resolver for Payments Tile
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_StateResolver
    extends Saas_Launcher_Model_Tile_ConfigBased_StateResolverAbstract
{
    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        $paymentConfigPaths = array(
            'paypal_express_checkout' => 'payment/paypal_express/active',
            'paypal_standard' => 'payment/paypal_standard/active',
            'paypal_payments_advanced' => 'payment/payflow_advanced/active',
            'paypal_payments_pro' => 'payment/paypal_direct/active',
            'paypal_payflow_link' => 'payment/payflow_link/active',
            'paypal_payflow_pro' => 'payment/verisign/active',
            'authorize_net' => 'payment/authorizenet/active',
        );
        $currentStore = $this->_app->getStore();
        // if at least one of the related payment methods is active then the tile is considered to be complete
        foreach ($paymentConfigPaths as $configPath) {
            if ((bool)$currentStore->getConfig($configPath)) {
                return true;
            }
        }
        return false;
    }
}

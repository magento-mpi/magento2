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
 * Payment configuration save handler factory
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerFactoryAbstract
{
    /**
     * Retrieve save handler ID - save handler class name map
     *
     * @return array
     */
    public function getSaveHandlerMap()
    {
        return array(
            'paypal_express_checkout'
                => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler',
            'paypal_payflow_link'
                => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandler',
            'paypal_payflow_pro'
                => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowProSaveHandler',
            'paypal_payments_advanced'
                 => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler',
            'paypal_payments_pro'
                => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler',
            'paypal_payments_standard'
                => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsStandardSaveHandler',
            'authorize_net'
                => 'Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler',
        );
    }
}

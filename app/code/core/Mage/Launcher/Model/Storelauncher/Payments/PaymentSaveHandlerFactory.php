<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment configuration save handler factory
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerFactoryAbstract
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
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler',
            'paypal_payflow_link'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandler',
            'paypal_payflow_pro'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowProSaveHandler',
            'paypal_payments_advanced'
                 => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler',
            'paypal_payments_pro'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler',
            'paypal_payments_standard'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsStandardSaveHandler',
            'authorize_net'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler',
        );
    }
}

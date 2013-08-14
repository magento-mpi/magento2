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
 * PayPal Express Checkout configuration save handler
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('payment');
    }

    /**
     * Prepare payment configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (!isset($data['groups']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'])
        ) {
            throw new Saas_Launcher_Exception('Email address is required.');
        }
        $accountEmail = trim($data['groups']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value']);

        if (!Zend_Validate::is($accountEmail, 'EmailAddress')) {
            throw new Saas_Launcher_Exception('Email address must have correct format.');
        }

        $preparedData['payment']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = $accountEmail;
        // enable PayPal Express Checkout
        $preparedData['payment']['paypal_alternative_payment_methods']['groups']['express_checkout_us']
            ['groups']['express_checkout_required']['fields']['enable_express_checkout']['value'] = 1;
        return $preparedData;
    }
}

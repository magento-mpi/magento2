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

class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        return new Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler(
            $config,
            $backendConfigModel
        );
    }

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    public function prepareDataValidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = 'test@example.com';

        $preparedData0 = array();
        $preparedData0['payment']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = 'test@example.com';
        $preparedData0['payment']['paypal_alternative_payment_methods']['groups']['express_checkout_us']
            ['groups']['express_checkout_required']['fields']['enable_express_checkout']['value']  = 1;

        return array(
            array($data0, $preparedData0, array('payment')),
        );
    }

    /**
     * This data provider emulates invalid input for prepareData method
     *
     * @return array
     */
    public function prepareDataInvalidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['paypal_alternative_payment_methods']['groups']
            ['express_checkout_us']['groups']['express_checkout_required']['groups']
            ['express_checkout_required_express_checkout']['fields']['business_account']['value'] = 'invalid_email';

        return array(
            array($data0),
        );
    }
}

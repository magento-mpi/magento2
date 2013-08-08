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

class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowProSaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Magento_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Magento_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        return new Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowProSaveHandler(
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
        $data0['groups']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields']
        = array(
            'partner' => array('value' => 'PayPal Partner '),
            'vendor' => array('value' => 'PayPal Vendor '),
            'user' => array('value' => 'user '),
            'pwd' => array('value' => 'password '),
        );


        $preparedFields['partner']['value'] = 'PayPal Partner';
        $preparedFields['vendor']['value'] = 'PayPal Vendor';
        $preparedFields['user']['value'] = 'user';
        $preparedFields['pwd']['value'] = 'password';
        $preparedData0 = array();
        $preparedData0['payment']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields'] = $preparedFields;
        $preparedData0['payment']['paypal_payments']['groups']['paypal_verisign']
            ['groups']['paypal_payflow_required']['fields']['enable_paypal_payflow']['value'] = 1;
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
        $validData = array();
        $validData['groups']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields']
        = array(
            'partner' => array('value' => 'PayPal Partner '),
            'vendor' => array('value' => 'PayPal Vendor '),
            'user' => array('value' => 'user '),
            'pwd' => array('value' => 'password '),
        );

        $data0 = $validData;
        unset($data0['groups']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields']['partner']);

        $data1 = $validData;
        unset($data1['groups']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields']['vendor']);

        $data2 = $validData;
        unset($data2['groups']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields']['user']);

        $data3 = $validData;
        unset($data3['groups']['paypal_payment_gateways']['groups']['paypal_verisign_with_express_checkout_us']
            ['groups']['paypal_payflow_required']['groups']['paypal_payflow_api_settings']['fields']['pwd']);

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
        );
    }
}

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

class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandlerTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Magento_Core_Model_Config $config,
        Magento_Backend_Model_Config $backendConfigModel
    ) {
        return new Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandler(
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
        $data0['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields']
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
        $preparedData0['payment']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields'] = $preparedFields;
        $preparedData0['payment']['paypal_payment_gateways']['groups']['payflow_link_us']['groups']
            ['payflow_link_required']['fields']['enable_payflow_link']['value'] = 1;
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
        $validData['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields']
        = array(
            'partner' => array('value' => 'PayPal Partner '),
            'vendor' => array('value' => 'PayPal Vendor '),
            'user' => array('value' => 'user '),
            'pwd' => array('value' => 'password '),
        );

        $data0 = $validData;
        unset($data0['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields']['partner']);

        $data1 = $validData;
        unset($data1['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields']['vendor']);

        $data2 = $validData;
        unset($data2['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields']['user']);

        $data3 = $validData;
        unset($data3['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields']['pwd']);

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
        );
    }
}

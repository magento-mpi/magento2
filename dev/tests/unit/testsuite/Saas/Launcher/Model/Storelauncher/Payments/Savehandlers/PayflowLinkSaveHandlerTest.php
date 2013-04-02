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
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
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
        $data0['groups']['payflow_link']['fields']['partner']['value'] = 'PayPal Partner ';
        $data0['groups']['payflow_link']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data0['groups']['payflow_link']['fields']['user']['value'] = 'user ';
        $data0['groups']['payflow_link']['fields']['pwd']['value'] = 'password ';

        $preparedData0 = array();
        $preparedData0['paypal']['payflow_link']['fields']['partner']['value'] = 'PayPal Partner';
        $preparedData0['paypal']['payflow_link']['fields']['vendor']['value'] = 'PayPal Vendor';
        $preparedData0['paypal']['payflow_link']['fields']['user']['value'] = 'user';
        $preparedData0['paypal']['payflow_link']['fields']['pwd']['value'] = 'password';
        $preparedData0['paypal']['global']['fields']['payflow_link']['value'] = 1;

        return array(
            array($data0, $preparedData0, array('paypal')),
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
        $validData['groups']['payflow_link']['fields']['partner']['value'] = 'PayPal Partner ';
        $validData['groups']['payflow_link']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $validData['groups']['payflow_link']['fields']['user']['value'] = 'user ';
        $validData['groups']['payflow_link']['fields']['pwd']['value'] = 'password ';

        $data0 = $validData;
        unset($data0['groups']['payflow_link']['fields']['partner']);

        $data1 = $validData;
        unset($data1['groups']['payflow_link']['fields']['vendor']);

        $data2 = $validData;
        unset($data2['groups']['payflow_link']['fields']['user']);

        $data3 = $validData;
        unset($data3['groups']['payflow_link']['fields']['pwd']);

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
        );
    }
}

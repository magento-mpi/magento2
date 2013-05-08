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

class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FedexSaveHandlerTest
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
        return new Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FedexSaveHandler(
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
        // data set #0
        $data0 = array();
        $data0['groups']['fedex']['fields']['account']['value'] = ' fedex account ';
        $data0['groups']['fedex']['fields']['meter_number']['value'] = ' meter number ';
        $data0['groups']['fedex']['fields']['key']['value'] = ' fedex key ';
        $data0['groups']['fedex']['fields']['password']['value'] = ' fedex password ';
        $data0['groups']['fedex']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['fedex']['fields']['account']['value'] = 'fedex account';
        $preparedData0['carriers']['fedex']['fields']['meter_number']['value'] = 'meter number';
        $preparedData0['carriers']['fedex']['fields']['key']['value'] = 'fedex key';
        $preparedData0['carriers']['fedex']['fields']['password']['value'] = 'fedex password';
        $preparedData0['carriers']['fedex']['fields']['active']['value'] = 1;
        $preparedData0['carriers']['fedex']['fields']['sandbox_mode']['value'] = 0;

        return array(
            array($data0, $preparedData0, array('carriers')),
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

        $data1 = array();
        $data1['groups']['fedex']['fields']['account']['value'] = ' fedex account ';
        $data1['groups']['fedex']['fields']['key']['value'] = ' fedex key ';
        $data1['groups']['fedex']['fields']['password']['value'] = ' fedex password ';

        $data2 = array();
        $data2['groups']['fedex']['fields']['meter_number']['value'] = ' meter number ';
        $data2['groups']['fedex']['fields']['key']['value'] = ' fedex key ';
        $data2['groups']['fedex']['fields']['password']['value'] = ' fedex password ';

        $data3 = array();
        $data3['groups']['fedex']['fields']['account']['value'] = ' fedex account ';
        $data3['groups']['fedex']['fields']['meter_number']['value'] = ' meter number ';
        $data3['groups']['fedex']['fields']['password']['value'] = ' fedex password ';

        $data4 = array();
        $data4['groups']['fedex']['fields']['account']['value'] = ' fedex account ';
        $data4['groups']['fedex']['fields']['meter_number']['value'] = ' meter number ';
        $data4['groups']['fedex']['fields']['key']['value'] = ' fedex key ';

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
            array($data4),
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandlerTest
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        return new Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler(
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
        $data0['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data0['groups']['flatrate']['fields']['type']['value'] = 'O';
        $data0['groups']['flatrate']['fields']['price']['value'] = ' price ';
        $data0['groups']['flatrate']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['flatrate']['fields']['name']['value'] = 'flate rate method name';
        $preparedData0['carriers']['flatrate']['fields']['type']['value'] = 'O';
        $preparedData0['carriers']['flatrate']['fields']['price']['value'] = 'price';
        $preparedData0['carriers']['flatrate']['fields']['active']['value'] = 1;

        // data set #1
        $data1 = $data0;
        $preparedData1 = $preparedData0;
        $data1['groups']['flatrate']['fields']['active']['value'] = '0';
        $data1['groups']['flatrate']['fields']['type']['value'] = 'I';
        $preparedData1['carriers']['flatrate']['fields']['active']['value'] = 0;
        $preparedData1['carriers']['flatrate']['fields']['type']['value'] = 'I';
        return array(
            array($data0, $preparedData0, array('carriers')),
            array($data1, $preparedData1, array('carriers')),
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
        $data1['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data1['groups']['flatrate']['fields']['type']['value'] = 'K'; // wrong type
        $data1['groups']['flatrate']['fields']['price']['value'] = ' price ';

        $data2 = array();
        $data2['groups']['flatrate']['fields']['type']['value'] = 'O';
        $data2['groups']['flatrate']['fields']['price']['value'] = ' price ';

        $data3 = array();
        $data3['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data3['groups']['flatrate']['fields']['price']['value'] = ' price ';

        $data4 = array();
        $data4['groups']['flatrate']['fields']['name']['value'] = ' flate rate method name ';
        $data4['groups']['flatrate']['fields']['type']['value'] = 'O';

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
            array($data4),
        );
    }
}

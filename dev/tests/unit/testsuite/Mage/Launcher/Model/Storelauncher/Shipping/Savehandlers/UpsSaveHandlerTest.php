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

class Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandlerTest
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
        return new Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandler(
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
        $data0['groups']['ups']['fields']['access_license_number']['value'] = ' account license number ';
        $data0['groups']['ups']['fields']['password']['value'] = ' ups password ';
        $data0['groups']['ups']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['ups']['fields']['access_license_number']['value'] = 'account license number';
        $preparedData0['carriers']['ups']['fields']['password']['value'] = 'ups password';
        $preparedData0['carriers']['ups']['fields']['type']['value'] = 'UPS_XML';
        $preparedData0['carriers']['ups']['fields']['active']['value'] = 1;

        // data set #1
        $data1 = $data0;
        $preparedData1 = $preparedData0;
        $data1['groups']['ups']['fields']['active']['value'] = '0';
        $preparedData1['carriers']['ups']['fields']['active']['value'] = 0;
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
        $data1['groups']['ups']['fields']['password']['value'] = ' ups password ';

        $data2 = array();
        $data2['groups']['ups']['fields']['access_license_number']['value'] = ' account license number ';

        return array(
            array($data0),
            array($data1),
            array($data2),
        );
    }
}

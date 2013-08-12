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

class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_DhlSaveHandlerTest
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
        return new Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_DhlSaveHandler(
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
        $data0['groups']['dhlint']['fields']['id']['value'] = ' dhl access id ';
        $data0['groups']['dhlint']['fields']['account']['value'] = ' dhl account number ';
        $data0['groups']['dhlint']['fields']['password']['value'] = ' dhl password ';
        $data0['groups']['dhlint']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['dhlint']['fields']['id']['value'] = 'dhl access id';
        $preparedData0['carriers']['dhlint']['fields']['account']['value'] = 'dhl account number';
        $preparedData0['carriers']['dhlint']['fields']['password']['value'] = 'dhl password';
        $preparedData0['carriers']['dhlint']['fields']['active']['value'] = 1;

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
        $data1['groups']['dhlint']['fields']['id']['value'] = ' dhl access id ';
        $data1['groups']['dhlint']['fields']['password']['value'] = ' dhl password ';

        $data2 = array();
        $data2['groups']['dhlint']['fields']['id']['value'] = ' dhl access id ';
        $data2['groups']['dhlint']['fields']['account']['value'] = ' dhl account number ';

        return array(
            array($data0),
            array($data1),
            array($data2),
        );
    }
}

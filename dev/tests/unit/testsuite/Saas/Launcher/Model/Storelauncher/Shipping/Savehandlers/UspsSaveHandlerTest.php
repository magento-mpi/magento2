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

class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UspsSaveHandlerTest
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
        return new Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UspsSaveHandler(
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
        $data0['groups']['usps']['fields']['userid']['value'] = ' user id ';
        $data0['groups']['usps']['fields']['password']['value'] = ' usps password ';
        $data0['groups']['usps']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['usps']['fields']['userid']['value'] = 'user id';
        $preparedData0['carriers']['usps']['fields']['password']['value'] = 'usps password';
        $preparedData0['carriers']['usps']['fields']['active']['value'] = 1;
        $preparedData0['carriers']['usps']['fields']['mode']['value'] = 1;

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
        $data1['groups']['usps']['fields']['password']['value'] = ' usps password ';

        $data2 = array();
        $data2['groups']['usps']['fields']['userid']['value'] = ' user id ';

        return array(
            array($data0),
            array($data1),
            array($data2),
        );
    }
}

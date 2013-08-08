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

class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandlerTest
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
        return new Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandler(
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
        $data0['groups']['ups']['fields']['username']['value'] = ' ups username ';
        $data0['groups']['ups']['fields']['active']['value'] = '1';

        $preparedData0 = array();
        $preparedData0['carriers']['ups']['fields']['access_license_number']['value'] = 'account license number';
        $preparedData0['carriers']['ups']['fields']['password']['value'] = 'ups password';
        $preparedData0['carriers']['ups']['fields']['username']['value'] = 'ups username';
        $preparedData0['carriers']['ups']['fields']['type']['value'] = 'UPS_XML';
        $preparedData0['carriers']['ups']['fields']['active']['value'] = 1;
        $preparedData0['carriers']['ups']['fields']['is_account_live']['value'] = 1;
        $preparedData0['carriers']['ups']['fields']['mode_xml']['value'] = 1;

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
        $data1['groups']['ups']['fields']['password']['value'] = ' ups password ';
        $data1['groups']['ups']['fields']['access_license_number']['value'] = ' account license number ';

        $data2 = array();
        $data2['groups']['ups']['fields']['password']['value'] = ' ups password ';
        $data2['groups']['ups']['fields']['username']['value'] = ' ups username ';

        $data3 = array();
        $data3['groups']['ups']['fields']['access_license_number']['value'] = ' account license number ';
        $data3['groups']['ups']['fields']['username']['value'] = ' ups username ';

        return array(
            array($data0),
            array($data1),
            array($data2),
            array($data3),
        );
    }
}

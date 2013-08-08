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

class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandlerTest
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
        return new Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler(
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
        $data0['groups']['authorizenet']['fields']['login']['value'] = 'API Login ';
        $data0['groups']['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key ';

        $preparedData0 = array();
        $preparedData0['payment']['authorizenet']['fields']['login']['value'] = 'API Login';
        $preparedData0['payment']['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key';
        $preparedData0['payment']['authorizenet']['fields']['active']['value'] = 1;

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
        $data0['groups']['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key ';

        $data1 = array();
        $data1['groups']['authorizenet']['fields']['login']['value'] = 'API Login ';

        return array(
            array($data0),
            array($data1),
        );
    }
}

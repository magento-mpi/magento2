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

class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandlerTest
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
        return new Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler(
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
        $data0['groups']['paypal_group_all_in_one']['groups']['wpp_us']['groups']['wpp_required_settings']
            ['groups']['wpp_and_express_checkout']['fields']['business_account']['value'] = 'test@example.com';

        $preparedData0 = array();
        $preparedData0['payment']['paypal_group_all_in_one']['groups']['wpp_us']['groups']['wpp_required_settings']
            ['groups']['wpp_and_express_checkout']['fields']['business_account']['value'] = 'test@example.com';
        $preparedData0['payment']['paypal_group_all_in_one']['groups']['wpp_us']['groups']
            ['wpp_required_settings']['fields']['enable_wpp']['value'] = 1;

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
        $data0['groups']['paypal_group_all_in_one']['groups']['wpp_us']['groups']['wpp_required_settings']
            ['groups']['wpp_and_express_checkout']['fields']['business_account']['value'] = 'email_invalid';

        return array(
            array($data0),
        );
    }
}

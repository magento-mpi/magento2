<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Payments Advanced configuration save handler
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('payment');
    }

    /**
     * Prepare payment configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (isset($data['groups']['paypal_group_all_in_one']['groups']['payflow_advanced_us']
            ['groups']['required_settings']['groups']['payments_advanced']['fields'])
        ) {
            $fields = $data['groups']['paypal_group_all_in_one']['groups']['payflow_advanced_us']
                ['groups']['required_settings']['groups']['payments_advanced']['fields'];
        }
        if (empty($fields['partner']['value'])) {
            throw new Saas_Launcher_Exception('Partner field is required.');
        }
        if (empty($fields['vendor']['value'])) {
            throw new Saas_Launcher_Exception('Vendor field is required.');
        }
        if (empty($fields['user']['value'])) {
            throw new Saas_Launcher_Exception('User field is required.');
        }
        if (empty($fields['pwd']['value'])) {
            throw new Saas_Launcher_Exception('Password field is required.');
        }

        $preparedFields['partner']['value'] = trim($fields['partner']['value']);
        $preparedFields['vendor']['value'] = trim($fields['vendor']['value']);
        $preparedFields['user']['value'] = trim($fields['user']['value']);
        $preparedFields['pwd']['value'] = trim($fields['pwd']['value']);
        $preparedData['payment']['paypal_group_all_in_one']['groups']['payflow_advanced_us']['groups']
            ['required_settings']['groups']['payments_advanced']['fields'] = $preparedFields;

        // enable PayPal Payments Advanced
        $preparedData['payment']['paypal_group_all_in_one']['groups']['payflow_advanced_us']['groups']
            ['required_settings']['fields']['enable_payflow_advanced']['value'] = 1;
        return $preparedData;
    }
}

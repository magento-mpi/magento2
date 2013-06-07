<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Payflow Link configuration save handler
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandler
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
        if (isset($data['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields'])
        ) {
            $fields = $data['groups']['paypal_payment_gateways']['groups']['payflow_link_us']
                ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields'];
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
        $preparedData['payment']['paypal_payment_gateways']['groups']['payflow_link_us']
            ['groups']['payflow_link_required']['groups']['payflow_link_payflow_link']['fields'] = $preparedFields;

        // enable PayPal Payflow Link
        $preparedData['payment']['paypal_payment_gateways']['groups']['payflow_link_us']['groups']
            ['payflow_link_required']['fields']['enable_payflow_link']['value'] = 1;
        return $preparedData;
    }
}

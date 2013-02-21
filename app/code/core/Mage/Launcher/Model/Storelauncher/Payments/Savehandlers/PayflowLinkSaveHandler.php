<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Payflow Link configuration save handler
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandler
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('paypal');
    }

    /**
     * Prepare payment configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Mage_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (empty($data['groups']['payflow_link']['fields']['partner']['value'])) {
            throw new Mage_Launcher_Exception('Partner field is required.');
        }
        if (empty($data['groups']['payflow_link']['fields']['vendor']['value'])) {
            throw new Mage_Launcher_Exception('Vendor field is required.');
        }
        if (empty($data['groups']['payflow_link']['fields']['user']['value'])) {
            throw new Mage_Launcher_Exception('User field is required.');
        }
        if (empty($data['groups']['payflow_link']['fields']['pwd']['value'])) {
            throw new Mage_Launcher_Exception('Password field is required.');
        }

        $preparedData['paypal']['payflow_link']['fields']['partner']['value'] =
            trim($data['groups']['payflow_link']['fields']['partner']['value']);
        $preparedData['paypal']['payflow_link']['fields']['vendor']['value'] =
            trim($data['groups']['payflow_link']['fields']['vendor']['value']);
        $preparedData['paypal']['payflow_link']['fields']['user']['value'] =
            trim($data['groups']['payflow_link']['fields']['user']['value']);
        $preparedData['paypal']['payflow_link']['fields']['pwd']['value'] =
            trim($data['groups']['payflow_link']['fields']['pwd']['value']);

        // enable PayPal Payflow Link
        $preparedData['paypal']['global']['fields']['payflow_link']['value'] = 1;
        return $preparedData;
    }
}

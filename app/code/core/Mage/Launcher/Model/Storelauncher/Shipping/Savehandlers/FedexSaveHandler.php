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
 * Fedex configuration save handler
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_FedexSaveHandler
    extends Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerAbstract
{
    /**
     * Retrieve name of the related configuration section
     *
     * @return string
     */
    public function getRelatedConfigSection()
    {
        return 'carriers';
    }

    /**
     * Prepare configuration data for saving
     *
     * @param array $data
     * @return array prepared data
     * @throws Mage_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (empty($data['groups']['fedex']['fields']['account']['value'])) {
            throw new Mage_Launcher_Exception('Account ID is required.');
        }
        if (empty($data['groups']['fedex']['fields']['meter_number']['value'])) {
            throw new Mage_Launcher_Exception('Meter Number is required.');
        }
        if (empty($data['groups']['fedex']['fields']['key']['value'])) {
            throw new Mage_Launcher_Exception('Key is required.');
        }
        if (empty($data['groups']['fedex']['fields']['password']['value'])) {
            throw new Mage_Launcher_Exception('Password is required.');
        }

        $preparedData['fedex']['fields']['account']['value'] =
            trim($data['groups']['fedex']['fields']['account']['value']);
        $preparedData['fedex']['fields']['meter_number']['value'] =
            trim($data['groups']['fedex']['fields']['meter_number']['value']);
        $preparedData['fedex']['fields']['key']['value'] =
            trim($data['groups']['fedex']['fields']['key']['value']);
        $preparedData['fedex']['fields']['password']['value'] =
            trim($data['groups']['fedex']['fields']['password']['value']);

        // enable Fedex for checkout if needed
        $isMethodEnabled = empty($data['groups']['fedex']['fields']['active']['value']) ? 0 : 1;
        $preparedData['fedex']['fields']['active']['value'] = $isMethodEnabled;

        return $preparedData;
    }
}

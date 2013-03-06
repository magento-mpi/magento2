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
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('carriers');
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

        $preparedData['carriers']['fedex']['fields']['account']['value'] =
            trim($data['groups']['fedex']['fields']['account']['value']);
        $preparedData['carriers']['fedex']['fields']['meter_number']['value'] =
            trim($data['groups']['fedex']['fields']['meter_number']['value']);
        $preparedData['carriers']['fedex']['fields']['key']['value'] =
            trim($data['groups']['fedex']['fields']['key']['value']);
        $preparedData['carriers']['fedex']['fields']['password']['value'] =
            trim($data['groups']['fedex']['fields']['password']['value']);

        // Enable Fedex for checkout
        $preparedData['carriers']['fedex']['fields']['active']['value'] = 1;

        return $preparedData;
    }
}

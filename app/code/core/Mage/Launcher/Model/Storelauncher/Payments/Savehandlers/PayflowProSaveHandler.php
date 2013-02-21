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
 * PayPal Payflow Pro configuration save handler
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowProSaveHandler
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
        if (empty($data['groups']['verisign']['fields']['partner']['value'])) {
            throw new Mage_Launcher_Exception('Partner field is required.');
        }
        if (empty($data['groups']['verisign']['fields']['vendor']['value'])) {
            throw new Mage_Launcher_Exception('Vendor field is required.');
        }
        if (empty($data['groups']['verisign']['fields']['user']['value'])) {
            throw new Mage_Launcher_Exception('User field is required.');
        }
        if (empty($data['groups']['verisign']['fields']['pwd']['value'])) {
            throw new Mage_Launcher_Exception('Password field is required.');
        }

        $preparedData['paypal']['verisign']['fields']['partner']['value'] =
            trim($data['groups']['verisign']['fields']['partner']['value']);
        $preparedData['paypal']['verisign']['fields']['vendor']['value'] =
            trim($data['groups']['verisign']['fields']['vendor']['value']);
        $preparedData['paypal']['verisign']['fields']['user']['value'] =
            trim($data['groups']['verisign']['fields']['user']['value']);
        $preparedData['paypal']['verisign']['fields']['pwd']['value'] =
            trim($data['groups']['verisign']['fields']['pwd']['value']);

        // enable PayPal Payflow Pro
        $preparedData['paypal']['global']['fields']['verisign']['value'] = 1;
        return $preparedData;
    }
}

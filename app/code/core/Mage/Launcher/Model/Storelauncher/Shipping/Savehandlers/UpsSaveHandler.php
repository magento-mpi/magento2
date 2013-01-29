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
 * UPS configuration save handler
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandler
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
        if (empty($data['groups']['ups']['fields']['access_license_number']['value'])) {
            throw new Mage_Launcher_Exception('Access License Number is required.');
        }
        if (empty($data['groups']['ups']['fields']['password']['value'])) {
            throw new Mage_Launcher_Exception('Password is required.');
        }

        $preparedData['ups']['fields']['access_license_number']['value'] =
            trim($data['groups']['ups']['fields']['access_license_number']['value']);
        $preparedData['ups']['fields']['password']['value'] =
            trim($data['groups']['ups']['fields']['password']['value']);

        // always choose not-deprecated type of UPS API
        $preparedData['ups']['fields']['type']['value'] = 'UPS_XML';
        // enable UPS for checkout if needed
        $isMethodEnabled = empty($data['groups']['ups']['fields']['active']['value']) ? 0 : 1;
        $preparedData['ups']['fields']['active']['value'] = $isMethodEnabled;

        return $preparedData;
    }
}

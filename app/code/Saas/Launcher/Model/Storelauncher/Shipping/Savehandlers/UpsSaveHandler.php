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
 * UPS configuration save handler
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
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
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (empty($data['groups']['ups']['fields']['access_license_number']['value'])) {
            throw new Saas_Launcher_Exception('Access License Number is required.');
        }
        if (empty($data['groups']['ups']['fields']['password']['value'])) {
            throw new Saas_Launcher_Exception('Password is required.');
        }

        if (empty($data['groups']['ups']['fields']['username']['value'])) {
            throw new Saas_Launcher_Exception('User ID is required.');
        }

        $preparedData['carriers']['ups']['fields']['access_license_number']['value'] =
            trim($data['groups']['ups']['fields']['access_license_number']['value']);
        $preparedData['carriers']['ups']['fields']['password']['value'] =
            trim($data['groups']['ups']['fields']['password']['value']);
        $preparedData['carriers']['ups']['fields']['username']['value'] =
            trim($data['groups']['ups']['fields']['username']['value']);

        // always choose not-deprecated type of UPS API
        $preparedData['carriers']['ups']['fields']['type']['value'] = 'UPS_XML';
        // Enable UPS for checkout in live mode
        $preparedData['carriers']['ups']['fields']['active']['value'] = 1;
        $preparedData['carriers']['ups']['fields']['is_account_live']['value'] = 1;
        $preparedData['carriers']['ups']['fields']['mode_xml']['value'] = 1;

        return $preparedData;
    }
}

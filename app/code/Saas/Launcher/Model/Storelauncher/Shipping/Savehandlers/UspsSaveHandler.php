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
 * USPS configuration save handler
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UspsSaveHandler
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
        if (empty($data['groups']['usps']['fields']['userid']['value'])) {
            throw new Saas_Launcher_Exception('User ID is required.');
        }
        if (empty($data['groups']['usps']['fields']['password']['value'])) {
            throw new Saas_Launcher_Exception('Password is required.');
        }

        $preparedData['carriers']['usps']['fields']['userid']['value'] =
            trim($data['groups']['usps']['fields']['userid']['value']);
        $preparedData['carriers']['usps']['fields']['password']['value'] =
            trim($data['groups']['usps']['fields']['password']['value']);

        // Enable USPS for checkout
        $preparedData['carriers']['usps']['fields']['active']['value'] = 1;
        $preparedData['carriers']['usps']['fields']['mode']['value'] = 1;

        return $preparedData;
    }
}

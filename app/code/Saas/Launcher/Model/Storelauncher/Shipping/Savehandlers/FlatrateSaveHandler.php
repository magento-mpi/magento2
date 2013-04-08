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
 * Flat Rate configuration save handler
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler
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
        if (empty($data['groups']['flatrate']['fields']['name']['value'])) {
            throw new Saas_Launcher_Exception('Display Name is required.');
        }
        if (empty($data['groups']['flatrate']['fields']['price']['value'])) {
            throw new Saas_Launcher_Exception('Price is required.');
        }
        if (!isset($data['groups']['flatrate']['fields']['type']['value'])
            || !in_array($data['groups']['flatrate']['fields']['type']['value'], array('', 'O', 'I'))
        ) {
            throw new Saas_Launcher_Exception('Type is required.');
        }

        $preparedData['carriers']['flatrate']['fields']['name']['value'] =
            trim($data['groups']['flatrate']['fields']['name']['value']);
        $preparedData['carriers']['flatrate']['fields']['price']['value'] =
            trim($data['groups']['flatrate']['fields']['price']['value']);
        $preparedData['carriers']['flatrate']['fields']['type']['value'] =
            trim($data['groups']['flatrate']['fields']['type']['value']);

        // Enable Flat Rate for checkout
        $preparedData['carriers']['flatrate']['fields']['active']['value'] = 1;

        return $preparedData;
    }
}

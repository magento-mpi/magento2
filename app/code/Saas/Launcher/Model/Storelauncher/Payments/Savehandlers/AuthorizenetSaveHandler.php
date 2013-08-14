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
 * Authorize.net configuration save handler
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler
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
        if (empty($data['groups']['authorizenet']['fields']['login']['value'])) {
            throw new Saas_Launcher_Exception('API Login ID is required.');
        }
        if (empty($data['groups']['authorizenet']['fields']['trans_key']['value'])) {
            throw new Saas_Launcher_Exception('Transaction Key is required.');
        }

        $preparedData['payment']['authorizenet']['fields']['login']['value'] =
            trim($data['groups']['authorizenet']['fields']['login']['value']);
        $preparedData['payment']['authorizenet']['fields']['trans_key']['value'] =
            trim($data['groups']['authorizenet']['fields']['trans_key']['value']);

        // enable Authorize.net
        $preparedData['payment']['authorizenet']['fields']['active']['value'] = 1;
        return $preparedData;
    }
}

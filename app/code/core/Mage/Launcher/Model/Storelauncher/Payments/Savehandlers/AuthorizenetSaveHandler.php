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
 * Authorize.net configuration save handler
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler
    extends Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandler
{
    /**
     * Save payment configuration data
     *
     * @param array $data
     * @return null
     * @throws Mage_Launcher_Exception
     */
    public function save(array $data)
    {
        $preparedData = $this->prepareData($data);
        $this->_backendConfigModel->setSection('payment')
            ->setGroups($preparedData)
            ->save();
        $this->_config->reinit();
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
        if (empty($data['groups']['authorizenet']['fields']['login']['value'])) {
            throw new Mage_Launcher_Exception('API Login ID is required.');
        }
        if (empty($data['groups']['authorizenet']['fields']['trans_key']['value'])) {
            throw new Mage_Launcher_Exception('Transaction Key is required.');
        }

        $preparedData['authorizenet']['fields']['login']['value'] =
            trim($data['groups']['authorizenet']['fields']['login']['value']);
        $preparedData['authorizenet']['fields']['trans_key']['value'] =
            trim($data['groups']['authorizenet']['fields']['trans_key']['value']);

        // enable Authorize.net
        $preparedData['authorizenet']['fields']['active']['value'] = 1;
        return $preparedData;
    }
}

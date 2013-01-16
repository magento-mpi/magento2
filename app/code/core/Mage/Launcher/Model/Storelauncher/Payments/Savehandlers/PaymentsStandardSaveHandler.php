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
 * PayPal Payments Standard configuration save handler
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsStandardSaveHandler
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
        $this->_backendConfigModel->setSection('paypal')
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
        if (!isset($data['groups']['account']['fields']['business_account']['value'])) {
            throw new Mage_Launcher_Exception('Email address is required.');
        }
        $accountEmail = trim($data['groups']['account']['fields']['business_account']['value']);

        if (!Zend_Validate::is($accountEmail, 'EmailAddress')) {
            throw new Mage_Launcher_Exception('Email address must have correct format.');
        }

        $preparedData['account']['fields']['business_account']['value'] = $accountEmail;
        // enable PayPal Payments Standard
        $preparedData['global']['fields']['wps']['value'] = 1;
        return $preparedData;
    }
}

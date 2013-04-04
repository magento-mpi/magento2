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
 * PayPal Payments Pro configuration save handler
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
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
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        $preparedData = array();
        if (!isset($data['groups']['account']['fields']['business_account']['value'])) {
            throw new Saas_Launcher_Exception('Email address is required.');
        }
        $accountEmail = trim($data['groups']['account']['fields']['business_account']['value']);

        if (!Zend_Validate::is($accountEmail, 'EmailAddress')) {
            throw new Saas_Launcher_Exception('Email address must have correct format.');
        }

        $preparedData['paypal']['account']['fields']['business_account']['value'] = $accountEmail;
        // enable PayPal Payments Pro
        $preparedData['paypal']['global']['fields']['wpp']['value'] = 1;
        return $preparedData;
    }
}

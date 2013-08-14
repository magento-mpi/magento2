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
 * Save handler for BusinessInfo Tile
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Businessinfo_SaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('general', 'trans_email', 'shipping');
    }

    /**
     * Prepare Address Data for system configuration
     *
     * @param array $data
     * @return array
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        if (!isset($data['groups']['trans_email']['ident_general']['fields']['email']['value'])) {
            throw new Saas_Launcher_Exception('Store Contact Email address is required.');
        }

        $storeContactEmail = trim($data['groups']['trans_email']['ident_general']['fields']['email']['value']);
        if (!Zend_Validate::is($storeContactEmail, 'EmailAddress')) {
            throw new Saas_Launcher_Exception('Email address must have correct format.');
        }

        $groups = $data['groups'];
        $data['region_id'] = isset($data['region_id']) ? $data['region_id'] : 0;
        $groups['general']['store_information']['fields']['street_line1']['value'] = $data['street_line1'];
        $groups['general']['store_information']['fields']['street_line2']['value'] = $data['street_line2'];
        $groups['general']['store_information']['fields']['city']['value'] = $data['city'];
        $groups['general']['store_information']['fields']['postcode']['value'] = $data['postcode'];
        $groups['general']['store_information']['fields']['region_id']['value'] = $data['region_id'];
        if (isset($data['use_for_shipping'])) {
            $storeInformation = $groups['general']['store_information']['fields'];
            $shipping = array(
                'country_id' => $storeInformation['country_id'],
                'region_id' => array('value' => $data['region_id']),
                'postcode' => array('value' => $data['postcode']),
                'city' => array('value' => $data['city']),
                'street_line1' => array('value' => $data['street_line1']),
                'street_line2' => array('value' => $data['street_line2'])
            );
            $groups['shipping']['origin']['fields'] = $shipping;
        }
        return $groups;
    }
}

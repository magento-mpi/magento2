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
 * Launcher data helper
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Prepare Address Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareAddressData($data)
    {
        $groups = $data['groups'];
        $region = Mage::getModel('Mage_Directory_Model_Region')->load($data['region_id'])->getName();
        $groups['general']['store_information']['fields']['address']['value'] =
            sprintf("%s\n%s\n%s\n%s\n%s",
                $data['street_line1'],
                $data['street_line2'],
                $data['city'],
                $data['postcode'],
                $region
            );

        if (isset($data['use_for_shipping'])) {
            $storeInformation = $groups['general']['store_information']['fields'];
            $shipping = array(
                'country_id' => $storeInformation['merchant_country'],
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

    /**
     * Get address data from system configuration
     *
     * @param Mage_Core_Model_Store_Config $config
     * @return array
     */
    public function getAddressData($config)
    {
        $addressData = array(
            'street_line1' => '',
            'street_line2' => '',
            'city' => '',
            'postcode' => '',
            'region_id' => ''
        );
        $useForShipping = false;

        $address = $config->getConfig('general/store_information/address');
        $addressValues = explode("\n", $address);
        $addressPresent = count($addressValues) == 5;

        if ($addressPresent) {
            $addressData = array_combine(array_keys($addressData), $addressValues);
        }

        $addressData['country_id'] = $config->getConfig('general/store_information/merchant_country');

        if ($addressPresent) {
            $regionId = Mage::getModel('Mage_Directory_Model_Region')
                ->loadByName($addressData['region_id'], $addressData['country_id'])
                ->getName();
            $addressData['region_id'] = !empty($regionId) ? $regionId : 0;

            $useForShipping = true;

            foreach ($addressData as $key => $val) {
                $useForShipping = $useForShipping && $val == $config->getConfig('shipping/origin/' . $key);
                if (!$useForShipping) {
                    break;
                }
            }
        }

        $addressData['use_for_shipping'] = $useForShipping;

        return $addressData;
    }

}

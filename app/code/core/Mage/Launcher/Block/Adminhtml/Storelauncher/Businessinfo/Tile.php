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
 * BusinessInfo Tile Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile extends Mage_Launcher_Block_Adminhtml_Tile
{
    /**
     * Template for Businessinfo Tile Block
     *
     * @var string
     */
    protected $_template = 'page/storelauncher/tile/businessinfo.phtml';

    /**
     * Prepare Address Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareAddressData($data)
    {
        $groups = $data['groups'];
        $data['region_id'] = isset($data['region_id']) ? $data['region_id'] : 0;
        $region = $this->_regionModel->load($data['region_id'])->getName();
        $groups['general']['store_information']['fields']['street_line1']['value'] = $data['street_line1'];
        $groups['general']['store_information']['fields']['street_line2']['value'] = $data['street_line2'];
        $groups['general']['store_information']['fields']['city']['value'] = $data['city'];
        $groups['general']['store_information']['fields']['postcode']['value'] = $data['postcode'];
        $groups['general']['store_information']['fields']['region_id']['value'] = $region;
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

    /**
     * Get Address
     *
     * @return string
     */
    public function getAddress()
    {
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/street_line1');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/street_line2');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/city');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/postcode');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/region_id');
        $addressValues[] = $this->_storeConfig->getConfig('trans_email/ident_general/email');
        return $addressValues;
    }
}

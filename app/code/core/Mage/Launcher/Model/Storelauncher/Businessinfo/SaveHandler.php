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
 * Save handler for BusinessInfo Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Businessinfo_SaveHandler implements Mage_Launcher_Model_Tile_SaveHandler
{
    /**
     * @var Mage_Backend_Model_Config
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param Mage_Backend_Model_Config $config
     */
    function __construct(
        Mage_Backend_Model_Config $config
    ) {
        $this->_config = $config;
    }

    /**
     * Save function handle the whole Tile save process
     *
     * @param array $data Request data
     */
    public function save($data)
    {
        $sections = array('general', 'trans_email', 'shipping');
        $preparedData = $this->prepareData($data);

        //Write all config data on the GLOBAL scope
        foreach ($sections as $section) {
            if (!empty($preparedData[$section])) {
                $this->_config->setSection($section)
                    ->setGroups($preparedData[$section])
                    ->save();
            }
        }
    }

    /**
     * Prepare Address Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
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


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
     * Region Model
     *
     * @var Mage_Directory_Model_Region
     */
    protected $_regionModel;

    /**
     * Constructor
     *
     * @param Mage_Backend_Model_Config $config
     * @param Mage_Directory_Model_Region $regionModel
     */
    function __construct(
        Mage_Backend_Model_Config $config,
        Mage_Directory_Model_Region $regionModel
    ) {
        $this->_config = $config;
        $this->_regionModel = $regionModel;
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
        $region = $this->_regionModel->load($data['region_id'])->getName();
        $groups['general']['store_information']['fields']['address']['value'] = sprintf(
            "%s\n%s\n%s\n%s\n%s",
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
}


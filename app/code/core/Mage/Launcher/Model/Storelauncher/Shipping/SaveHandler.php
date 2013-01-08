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
 * Save handler for Shipping Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler implements Mage_Launcher_Model_Tile_SaveHandler
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
    function __construct(Mage_Backend_Model_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Save function handle the whole Tile save process
     *
     * @param array $data Request data
     */
    public function save($data)
    {

    }

    /**
     * Prepare Data
     *
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {

    }

    /**
     * Save function handle the origin address process
     *
     * @param array $data Request data
     */
    public function saveOriginAddress($data)
    {
        $this->_config->setSection('shipping')
            ->setGroups($this->prepareOriginAddressData($data))
            ->save();
    }

    /**
     * Prepare Origin Address Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareOriginAddressData($data)
    {
        $originAddressData['origin']['fields'] = array(
            'country_id' => array('value' => $data['country_id']),
            'region_id' => array('value' => $data['region_id']),
            'postcode' => array('value' => $data['postcode']),
            'city' => array('value' => $data['city']),
            'street_line1' => array('value' => $data['street_line1']),
            'street_line2' => array('value' => $data['street_line2'])
        );
        return $originAddressData;
    }

}


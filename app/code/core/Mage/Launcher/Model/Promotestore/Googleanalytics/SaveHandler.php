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
 * Save handler for Googleanalytics Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Promotestore_Googleanalytics_SaveHandler implements Mage_Launcher_Model_Tile_SaveHandler
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
        $section = 'google';
        $preparedData = $this->prepareData($data);

        //Write all config data on the GLOBAL scope
        if (!empty($preparedData[$section])) {
            $this->_config->setSection($section)
                ->setGroups($preparedData[$section])
                ->save();
        }
    }

    /**
     * Prepare Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        $groups = $data['groups'];
        if (isset($groups['google']['analytics']['fields']['account']['value'])
            && !empty($groups['google']['analytics']['fields']['account']['value'])) {
            $groups['google']['analytics']['fields']['active']['value'] = 1;
        }
        return $groups;
    }
}

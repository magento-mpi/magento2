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
 * Save handler for Design Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Design_SaveHandler extends Mage_Launcher_Model_Tile_MinimalSaveHandler
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
        $sections = array('general', 'design');
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
     * Prepare Data for system configuration
     *
     * @param array $data
     * @return array
     * @throws Mage_Launcher_Exception
     */
    public function prepareData($data)
    {
        $groups = $data['groups'];
        if (empty($groups['design']['theme']['fields']['theme_id']['value'])) {
            throw new Mage_Launcher_Exception('Theme is required.');
        }
        if (empty($groups['general']['store_information']['fields']['name']['value'])) {
            throw new Mage_Launcher_Exception('Store name is required.');
        }

        return $groups;
    }
}

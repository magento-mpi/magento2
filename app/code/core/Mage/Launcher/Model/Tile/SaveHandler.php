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
 * Tile Save Handler interface
 *
 * Class that implements this interface is fully responsible for preparing and saving data, on which this tile depends
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Launcher_Model_Tile_SaveHandler
{
    /**
     * Save function should be implemented by each of SaveHandler Tile Model
     *
     * @param array $data Request data
     */
    public function save(array $data);

    /**
     * Prepare Data for storing
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data);
}

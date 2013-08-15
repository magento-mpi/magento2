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
 * Minimal save handler
 *
 * This save handler does not save any information it is given
 * It can be used by tiles that do not store any information when drawer 'Save' button is clicked
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Tile_MinimalSaveHandler implements Saas_Launcher_Model_Tile_SaveHandler
{
    /**
     * Save data related to tile
     *
     * @param array $data request data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save(array $data)
    {
    }

    /**
     * Prepare data for saving
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        return $data;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Concrete save handler stub
 */
class Mage_Launcher_Model_Tile_SaveHandlerStub implements Mage_Launcher_Model_Tile_SaveHandler
{
    /**
     * Save function
     *
     * @param array $data Request data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save(array $data)
    {
    }

    /**
     * Prepare Data for storing
     *
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareData(array $data)
    {
        return $data;
    }
}

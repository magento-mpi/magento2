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
 * Concrete state resolver stub
 */
class Mage_Launcher_Model_Tile_StateResolverStub implements Mage_Launcher_Model_Tile_StateResolver
{
    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        return true;
    }

    /**
     * Handle System Configuration change (handle related event) and return new state
     *
     * @param string $sectionName
     * @param int $currentState current state of the tile
     * @return int result state
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleSystemConfigChange($sectionName, $currentState)
    {
        return Mage_Launcher_Model_Tile::STATE_COMPLETE;
    }
}

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
 * Minimal state resolver
 *
 * This save handler can be used by tiles that are considered to be complete when drawer 'Save' button is clicked
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Tile_MinimalStateResolver implements Saas_Launcher_Model_Tile_StateResolver
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
        return $currentState;
    }

    /**
     * Get Persistent State of the Tile
     *
     * @return int
     */
    public function getPersistentState()
    {
        if ($this->isTileComplete()) {
            return Saas_Launcher_Model_Tile::STATE_COMPLETE;
        }
        return Saas_Launcher_Model_Tile::STATE_TODO;
    }
}

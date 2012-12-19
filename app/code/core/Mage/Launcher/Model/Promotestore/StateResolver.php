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
 * State resolver for Contentpages Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Promotestore_StateResolver implements Mage_Launcher_Model_Tile_StateResolver
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
            return Mage_Launcher_Model_Tile::STATE_COMPLETE;
        }
        return Mage_Launcher_Model_Tile::STATE_TODO;
    }
}

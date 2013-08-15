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
 * Tile state resolver interface
 *
 * Class that implements this interface is fully responsible for identifying of correct state of the tile related to it
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Saas_Launcher_Model_Tile_StateResolver
{
    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete();

    /**
     * Handle System Configuration change (handle related event) and return new state
     *
     * @param string $sectionName
     * @param int $currentState current state of the tile
     * @return int result state
     */
    public function handleSystemConfigChange($sectionName, $currentState);

    /**
     * Get Persistent State
     *
     * @return int
     */
    public function getPersistentState();
}

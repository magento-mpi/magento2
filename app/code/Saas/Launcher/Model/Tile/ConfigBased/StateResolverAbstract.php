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
 * Abstract state resolver for configuration based tiles
 *
 * Class that extends this state resolver must at least override its isTileComplete method
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Saas_Launcher_Model_Tile_ConfigBased_StateResolverAbstract
    extends Saas_Launcher_Model_Tile_MinimalStateResolver
{
    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * Config sections that are related to the current Tile
     *
     * @var array
     */
    protected $_sections = array();

    /**
     * Constructor
     *
     * @param Magento_Core_Model_App $app
     */
    public function __construct(Magento_Core_Model_App $app)
    {
        $this->_app = $app;
    }

    /**
     * Handle System Configuration change (handle related event) and return new state
     *
     * @param string $sectionName
     * @param int $currentState current state of the tile
     * @return int result refreshed state
     */
    public function handleSystemConfigChange($sectionName, $currentState)
    {
        if (in_array($sectionName, $this->_sections)) {
            if ($this->isTileComplete()) {
                return Saas_Launcher_Model_Tile::STATE_COMPLETE;
            }
            if ($currentState == Saas_Launcher_Model_Tile::STATE_COMPLETE) {
                return Saas_Launcher_Model_Tile::STATE_TODO;
            }
        }
        return $currentState;
    }
}

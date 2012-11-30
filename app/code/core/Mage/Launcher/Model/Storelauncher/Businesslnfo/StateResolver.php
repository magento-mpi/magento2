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
 * State resolver for BusinessInfo Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Businessinfo_StateResolver
    implements Mage_Launcher_Model_Tile_StateResolver
{
    /**
     * Resolve state
     *
     * @return int identified state
     */
    public function resolve()
    {

    }

    /**
     * Handle System Configuration change (handle related event) and return new state
     *
     * @param Mage_Core_Model_Config $config
     * @param string $sectionName
     * @return int result state
     */
    public function handleSystemConfigChange(Mage_Core_Model_Config $config, $sectionName)
    {

    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
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
    * @return int identified state
    */
    public function resolve()
    {
        return Mage_Launcher_Model_Tile::STATE_COMPLETE;
    }

   /**
    * Handle System Configuration change (handle related event) and return new state
    *
    * @param Mage_Core_Model_Config $config
    * @param string $sectionName
    * @return int result state
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function handleSystemConfigChange(Mage_Core_Model_Config $config, $sectionName)
    {
        return Mage_Launcher_Model_Tile::STATE_COMPLETE;
    }
}

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
 * Landing page tile resource model
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Resource_Tile extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('launcher_tile', 'tile_id');
    }

    /**
     * Load landing page tile by its code
     *
     * @param Mage_Launcher_Model_Tile $tile
     * @param string $code
     * @return Mage_Launcher_Model_Resource_Tile
     */
    public function loadByCode($tile, $code)
    {
        return $this->load($tile, $code, 'code');
    }
}

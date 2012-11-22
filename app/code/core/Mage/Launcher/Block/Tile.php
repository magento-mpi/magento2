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
 * Tile Abstract Block
 *
 * @method Mage_Launcher_Model_Tile getTile()
 * @method Mage_Launcher_Block_Tile setTile(Mage_Launcher_Model_Tile $value)
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Tile extends Mage_Backend_Block_Abstract
{
    /**
     * Get Tile Code
     *
     * @return string
     */
    public function getTileCode()
    {
        return $this->getTile()->getCode();
    }

    /**
     * Get Tile State
     *
     * @return int
     */
    public function getTileState()
    {
        return $this->getTile()->getState();
    }

}

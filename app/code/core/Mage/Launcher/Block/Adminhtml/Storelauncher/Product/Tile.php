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
 * Product Tile Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Product_Tile extends Mage_Launcher_Block_Adminhtml_Tile
{
    /**
     * Retrieve the number of products created in the system
     *
     * @return int
     */
    public function getProductCount()
    {
        return $this->getTile()->getStateResolver()->getProductCount();
    }

    /**
     * Get Tile State
     *
     * @throws Mage_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $tileState = parent::getTileState();
        // This logic has been added for optimization purposes (in order not to listen to product creation events)
        // Product tile is considered complete even when product is created not from the Product Tile
        if (!$this->getTile()->isComplete()) {
            $this->getTile()->refreshState();
            $tileState = $this->getTile()->getState();
        }
        return $tileState;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tile Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile extends Saas_Launcher_Block_Adminhtml_Tile
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
     * @throws Saas_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $this->getTile()->refreshState();
        return parent::getTileState();
    }
}

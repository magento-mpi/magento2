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
 * Shipping Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
{
    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('Shipping');
    }

    /**
     * Check whether shipping is enabled
     *
     * @return boolean
     */
    public function isShippingEnabled()
    {
        if ($this->getTileState() == Mage_Launcher_Model_Tile::STATE_COMPLETE
            && !$this->getTile()->getStateResolver()->isShippingConfigured()
        ) {
            return false;
        }
        return true;
    }
}

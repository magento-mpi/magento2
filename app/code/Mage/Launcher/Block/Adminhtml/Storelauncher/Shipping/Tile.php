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
 * Shipping Tile Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Shipping_Tile extends Mage_Launcher_Block_Adminhtml_Tile
{
    /**
     * Get a list of configured Shipping methods
     *
     * @return array
     */
    public function getConfiguredShippingMethods()
    {
        return $this->getTile()->getStateResolver()->getConfiguredShippingMethods();
    }
}
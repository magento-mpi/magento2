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
 * Shipping Tile Block
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Tile extends Saas_Launcher_Block_Adminhtml_Tile
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

<?php
/**
 * Stub for Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_Grid methods _getBundleTierPriceInfo, _getTierPriceInfo
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
{
    /**
     * @var Mage_Core_Model_Factory_Helper
     */
    public $_helperFactory;

    /**
     * Changing _getBundleTierPriceInfo protected to public
     * Get tier price info to display in grid for Bundle product
     *
     * @param array $prices
     * @return array
     */
    public function getBundleTierPriceInfo($prices)
    {
        return parent::_getBundleTierPriceInfo($prices);
    }

    /**
     * Changing _getTierPriceInfo protected to public
     * Get tier price info to display in grid
     *
     * @param array $prices
     * @return array
     */
    public function getTierPriceInfo($prices)
    {
        return parent::_getTierPriceInfo($prices);
    }
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stub for Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_Grid methods _getBundleTierPriceInfo, _getTierPriceInfo
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

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout order review
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Onepage_Review_Info extends Mage_Sales_Block_Items_Abstract
{
    public function getItems()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote()->getAllVisibleItems();
    }

    public function getTotals()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote()->getTotals();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout order review
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Review_Info extends Magento_Sales_Block_Items_Abstract
{
    public function getItems()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote()->getAllVisibleItems();
    }

    public function getTotals()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote()->getTotals();
    }
}

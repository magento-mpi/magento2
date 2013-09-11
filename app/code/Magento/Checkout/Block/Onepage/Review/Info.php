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
namespace Magento\Checkout\Block\Onepage\Review;

class Info extends \Magento\Sales\Block\Items\AbstractItems
{
    public function getItems()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote()->getAllVisibleItems();
    }

    public function getTotals()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote()->getTotals();
    }
}

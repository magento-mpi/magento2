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
 * One page checkout cart link
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Onepage;

class Link extends \Magento\Core\Block\Template
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    public function isDisabled()
    {
        return !\Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote()->validateMinimumAmount();
    }

    public function isPossibleOnepageCheckout()
    {
        return $this->helper('Magento\Checkout\Helper\Data')->canOnepageCheckout();
    }
}

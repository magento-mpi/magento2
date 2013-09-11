<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Checkout shortcut link
 *
 * @category   Magento
 * @package    Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleCheckout\Block;

class Link extends \Magento\Core\Block\Template
{
    public function getImageStyle()
    {
        $s = \Mage::getStoreConfig('google/checkout/checkout_image');
        if (!$s) {
            $s = '180/46/trans';
        }
        return explode('/', $s);
    }

    public function getImageUrl()
    {
        $url = 'https://checkout.google.com/buttons/checkout.gif';
        $url .= '?merchant_id='.Mage::getStoreConfig('google/checkout/merchant_id');
        $v = $this->getImageStyle();
        $url .= '&w='.$v[0].'&h='.$v[1].'&style='.$v[2];
        $url .= '&variant='.($this->getIsDisabled() ? 'disabled' : 'text');
        $url .= '&loc='.Mage::getStoreConfig('google/checkout/locale');
        return $url;
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('googlecheckout/redirect/checkout');
    }

    public function getImageWidth()
    {
         $v = $this->getImageStyle();
         return $v[0];
    }

    public function getImageHeight()
    {
         $v = $this->getImageStyle();
         return $v[1];
    }

    /**
     * Check whether method is available and render HTML
     * @return string
     */
    public function _toHtml()
    {
        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
        if (\Mage::getModel('\Magento\GoogleCheckout\Model\Payment')->isAvailable($quote) && $quote->validateMinimumAmount()) {
            \Mage::dispatchEvent('googlecheckout_block_link_html_before', array('block' => $this));
            return parent::_toHtml();
        }
        return '';
    }

    public function getIsDisabled()
    {
        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
        /* @var $quote \Magento\Sales\Model\Quote */
        foreach ($quote->getAllVisibleItems() as $item) {
            /* @var $item \Magento\Sales\Model\Quote\Item */
            if (!$item->getProduct()->getEnableGooglecheckout()) {
                return true;
            }
        }
        return false;
    }
}

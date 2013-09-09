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
class Magento_GoogleCheckout_Block_Link extends Magento_Core_Block_Template
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($coreData, $context, $data);
    }

    public function getImageStyle()
    {
        $s = Mage::getStoreConfig('google/checkout/checkout_image');
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
        $quote = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
        if (Mage::getModel('Magento_GoogleCheckout_Model_Payment')->isAvailable($quote) && $quote->validateMinimumAmount()) {
            $this->_eventManager->dispatch('googlecheckout_block_link_html_before', array('block' => $this));
            return parent::_toHtml();
        }
        return '';
    }

    public function getIsDisabled()
    {
        $quote = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
        /* @var $quote Magento_Sales_Model_Quote */
        foreach ($quote->getAllVisibleItems() as $item) {
            /* @var $item Magento_Sales_Model_Quote_Item */
            if (!$item->getProduct()->getEnableGooglecheckout()) {
                return true;
            }
        }
        return false;
    }
}

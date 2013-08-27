<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Paypal Direct payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
    extends Enterprise_Pbridge_Block_Payment_Form_Abstract
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_WPP_DIRECT;

    /**
     * Adminhtml template for payment form block
     *
     * @var string
     */
    protected $_template = 'Enterprise_Pbridge::sales/order/create/pbridge.phtml';

    /**
     * Adminhtml Iframe block type
     *
     * @var string
     */
    protected $_iframeBlockType = 'Magento_Adminhtml_Block_Template';

    /**
     * Adminhtml iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Enterprise_Pbridge::iframe.phtml';

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('*/pbridge/result',
            array('store' => $this->getQuote()->getStoreId())
        );
    }

    /**
     * Getter
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->getQuote();
    }

    /**
     * Generate and return variation code
     *
     * @return string
     */
    protected function _getVariation()
    {
        return Mage::app()->getConfig()->getNode('default/payment/pbridge/merchantcode')
            . '_' . $this->getQuote()->getStore()->getWebsite()->getCode();
    }

    /**
     * Disable external CSS in admin order creation
     * @return null
     */
    public function getCssUrl()
    {
        return null;
    }

    /**
     * Get current customer object
     *
     * @return null|Magento_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        if (Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->getCustomer() instanceof Magento_Customer_Model_Customer) {
            return Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->getCustomer();
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getCurrentStore()
    {
        return $this->getQuote()->getStore();
    }
}

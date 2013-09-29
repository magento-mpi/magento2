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
 * Multishipping cart link
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class Link extends \Magento\Core\Block\Template
{
    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($coreData, $context, $data);
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }

    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    public function _toHtml()
    {
        if (!$this->_checkoutData->isMultishippingCheckoutAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
}

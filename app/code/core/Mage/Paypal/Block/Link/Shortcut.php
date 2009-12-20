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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal shortcut link
 */
class Mage_Paypal_Block_Link_Shortcut extends Mage_Core_Block_Template
{
    /**
     * Config instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * Express checkout URL getter
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('paypal/express/start');
    }

    /**
     * Get checkout button image url
     *
     * @return string
     */
    public function getImageUrl()
    {
        return Mage::getModel('paypal/express_checkout', array(
            'quote'  => Mage::getSingleton('checkout/session')->getQuote(),
            'config' => $this->_getConfig(),
        ))->getCheckoutShortcutImageUrl();
    }

    /**
     * Check whether method is available and render HTML
     * TODO: payment method instance is not supposed to know about quote.
     * The block also is not supposed to know about payment method instance
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_getConfig()->visibleOnCart) {
            return '';
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->validateMinimumAmount()
            || !Mage::getModel('paypal/express')->setConfig($this->_config)->isAvailable($quote)) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Config instance getter
     *
     * @return Mage_Paypal_Model_Config
     */
    protected function _getConfig()
    {
        if (null === $this->_config) {
            $this->_config = Mage::getModel('paypal/config', array(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS));
        }
        return $this->_config;
    }
}

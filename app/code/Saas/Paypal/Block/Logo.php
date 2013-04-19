<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * PayPal online logo with additional options
 */
class Saas_Paypal_Block_Logo extends Mage_Paypal_Block_Logo
{
    /**
     * Return ALT for Paypal Landing page
     *
     * @return string
     */
    public function getAlt()
    {
        /** @var $paypalHelper Mage_Paypal_Helper_Data */
        $paypalHelper = Mage::helper('Mage_Paypal_Helper_Data');
        if ($this->_getConfig()->getMerchantCountry() == Saas_Paypal_Model_Config::LOCALE_DE) {
            return $paypalHelper->__('PayPal empfohlen');
        }
        return $paypalHelper->__('Additional Options');
    }
}

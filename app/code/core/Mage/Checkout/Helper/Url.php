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
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Checkout url helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Helper_Url extends Mage_Core_Helper_Url
{
    /**
     * Retrieve shpping cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return $this->_getUrl('checkout/cart');
    }
    
    /**
     * Retrieve checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->_getUrl('checkout/onepage');
    }
    
    /**
     * Multi Shipping (MS) checkout urls
     */
    
    /**
     * Retrieve multishipping checkout url
     *
     * @return string
     */
    public function getMSCheckoutUrl()
    {
        return $this->_getUrl('checkout/multishipping');
    }
    
    public function getMSLoginUrl()
    {
        return $this->_getUrl('checkout/multishipping/login', array('_secure'=>true, '_current'=>true));
    }
    
    public function getMSAddressesUrl()
    {
        return $this->_getUrl('checkout/multishipping/addresses');
    }
    
    public function getMSRegisterUrl()
    {
        return $this->_getUrl('checkout/multishipping/register');
    }
    
    /**
     * One Page checkout urls
     */
    public function getOPCheckoutUrl()
    {
        return $this->_getUrl('checkout/onepage');
    }
}

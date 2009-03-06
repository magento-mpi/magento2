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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for AmazonPayments API wrappers
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Asp_Amount
{
    
	private $_value;
    private $_currencyCode;
	protected $_amountStringTemplate = "/^([A-Z]{3})\s([0-9]{1,}|[0-9]{1,}[.][0-9]{1,})$/";
    protected $_valueStringTemplate = "/^([0-9]{1,}|[0-9]{1,}[.][0-9]{1,})$/";
    protected $_currencyCodeStringTemplate = "/^([A-Z]{3})$/";
    
    public function init($amount)
    {
        $tmpArr = array();
        if (!preg_match($this->_amountStringTemplate, $amount, $tmpArr)) {
            return false;
        }
        $this->_value = $tmpArr[2];
        $this->_currencyCode = $tmpArr[1];
        return $this;
    }

    public function setValue($value)
    {
        $tmpArr = array();
        if (!preg_match($this->_valueStringTemplate, $value, $tmpArr)) {
            return false;
        }
        $this->_value = $tmpArr[1];
        return $this;
    }

    public function setCurrencyCode($currencyCode)
    {
        $tmpArr = array();
        if (!preg_match($this->_currencyCodeStringTemplate, $currencyCode, $tmpArr)) {
            return false;
        }
        $this->_currencyCode = $tmpArr[1];
        return $this;
    }

    public function getValue()
    {
    	return $this->_value;
    }
    
    public function getCurrencyCode()
    {
        return $this->_currencyCode;
    }
    
    public function toString()
    {
        return $this->getCurrencyCode() . ' ' . $this->getValue();
    }
}

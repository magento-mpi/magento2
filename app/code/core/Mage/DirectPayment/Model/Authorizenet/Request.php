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
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_DirectPayment_Model_Authorizenet_Request extends Varien_Object
{
    protected $_transKey = null;
    
    public function getTransactionKey()
    {
        return $this->_transKey;
    }
    
    public function setTransactionKey($transKey)
    {
        $this->_transKey = $transKey;
        return $this;
    }
    
    
    public function setConstantData(Mage_DirectPayment_Model_Authorizenet $paymentMethod)
    {
        $this->setXVersion('3.1')
            ->setXDelimData('FALSE')
            ->setXRelayResponse('TRUE');

        $this->setXTestRequest($paymentMethod->getConfigData('test') ? 'TRUE' : 'FALSE');

        $this->setXLogin($paymentMethod->getConfigData('login'))
            ->setXType('AUTH_ONLY')
            ->setXMethod('CC')
            ->setXRelayUrl(Mage::getBaseUrl().'directpayment/paygate/place');
            
        $this->setTransactionKey($paymentMethod->getConfigData('trans_key'));
        return $this;
    }
    
    public function setDataFromQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->setXFpSequence($quote->reserveOrderId()->getReservedOrderId());
        $amount = $quote->getBaseGrandTotal();
        $this->setXAmount($amount);
        $this->setXCurrencyCode($quote->getBaseCurrencyCode());
     
        $fp_timestamp = time();
        $hash = hash_hmac("md5", $this->getXLogin() . "^" . $this->getXFpSequence() . "^" . $fp_timestamp . "^" . $amount . "^", $this->getTransactionKey());
        $this->setXFpTimestamp($fp_timestamp);
        $this->setXFpHash($hash);
        
        return $this;
    }
}
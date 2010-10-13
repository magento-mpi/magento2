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
    
    /**
     * Return merchant transaction key.
     * Needed to generate sign.
     *
     * @return string
     */
    public function getTransactionKey()
    {
        return $this->_transKey;
    }
    
    /**
     * Set merchant transaction key.
     * Needed to generate sign.
     *
     * @param string $transKey
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function setTransactionKey($transKey)
    {
        $this->_transKey = $transKey;
        return $this;
    }
    
	/**
     * Generates the fingerprint for request.
     *
     * @param string $merchantApiLoginId
     * @param string $merchantTransactionKey
     * @param string $amount
     * @param string $fpSequence An invoice number or random number.
     * @param string $fpTimestamp
     * @return string The fingerprint.
     */
    public static function generateRequestSign($merchantApiLoginId, $merchantTransactionKey, $amount, $fpSequence, $fpTimestamp)
    {
        if (phpversion() >= '5.1.2'){
            return hash_hmac("md5", $merchantApiLoginId . "^" . $fpSequence . "^" . $fpTimestamp . "^" . $amount . "^", $merchantTransactionKey);
        }
        return bin2hex(mhash(MHASH_MD5, $merchantApiLoginId . "^" . $fpSequence . "^" . $fpTimestamp . "^" . $amount . "^", $merchantTransactionKey));
    }
    
    /**
     * Set paygate data to request.
     *
     * @param Mage_DirectPayment_Model_Authorizenet $paymentMethod
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function setConstantData(Mage_DirectPayment_Model_Authorizenet $paymentMethod)
    {
        $this->setXVersion('3.1')
            ->setXDelimData('FALSE')
            ->setXDelimData('TRUE')
            ->setXDelimChar(',')
            ->setXRelayResponse('TRUE');

        $this->setXTestRequest($paymentMethod->getConfigData('test') ? 'TRUE' : 'FALSE');

        $this->setXLogin($paymentMethod->getConfigData('login'))
            //->setXType('AUTH_ONLY')
            ->setXMethod(Mage_Paygate_Model_Authorizenet::REQUEST_METHOD_CC)
            ->setXRelayUrl(Mage::getBaseUrl().'directpayment/paygate/place');
            
        $this->setTransactionKey($paymentMethod->getConfigData('trans_key'));
        return $this;
    }
    
    /**
     * Set order data to request
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function setDataFromOrder(Mage_Sales_Model_Order $order)
    {
        $this->setXFpSequence($order->getId());
        $this->setXInvoiceNum($order->getIncrementId());
        $amount = $order->getBaseGrandTotal();
        $this->setXAmount($amount);
        $this->setXCurrencyCode($order->getBaseCurrencyCode());
        return $this;
    }
    
    /**
     * Set sign hash into the request object.
     * All needed fields should be placed in the object fist.
     *
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function signRequestData()
    {
        $fpTimestamp = time();
        $hash = self::generateRequestSign($this->getXLogin(), $this->getTransactionKey(), $this->getXAmount(), $this->getXFpSequence(), $fpTimestamp);
        $this->setXFpTimestamp($fpTimestamp);
        $this->setXFpHash($hash);
        return $this;
    }
    
    
}
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
class Mage_AmazonPayments_Model_Api_Asp_Ipn_Request extends Varien_Object
{
	const STATUS_CANCEL = 'A'; 
    const STATUS_RESERVE_SUCCESSFUL = 'PR'; 
    const STATUS_PAYMENT_INITIATED = 'PI'; 
    const STATUS_PAYMENT_SUCCESSFUL = 'PS'; 
    const STATUS_PAYMENT_FAILED = 'PF'; 
    const STATUS_REFUND_SUCCESSFUL = 'RS'; 
    const STATUS_REFUND_FAILED = 'RF'; 
    const STATUS_SYSTEM_ERROR = 'SE'; 

    private $requestParams;
    
    public function init($requestParams)
    {
    	if (!$this->_validateRequestParams($requestParams)) {
        	return false;
        }	
        $this->requestParams = $requestParams;
        $this->_setRequestParamsToData($this->_convertRequestParams($requestParams));
        return $this;
    }
    
    private function _validateRequestParams($requestParams)
    {
    	if (!isset($requestParams['referenceId']) ||
            !isset($requestParams['transactionAmount']) ||
            !isset($requestParams['transactionDate']) ||
            !isset($requestParams['status'])) {
            	return false;
        }
    
        $statusCode = $requestParams['status'];
        if ($statusCode != self::STATUS_CANCEL &&
            $statusCode != self::STATUS_RESERVE_SUCCESSFUL &&
            $statusCode != self::STATUS_PAYMENT_INITIATED &&
            $statusCode != self::STATUS_PAYMENT_SUCCESSFUL &&
            $statusCode != self::STATUS_PAYMENT_FAILED &&
            $statusCode != self::STATUS_REFUND_SUCCESSFUL &&
            $statusCode != self::STATUS_REFUND_FAILED &&
            $statusCode != self::STATUS_SYSTEM_ERROR) {
                return false;
        }

        if (($statusCode == self::STATUS_RESERVE_SUCCESSFUL ||
             $statusCode == self::STATUS_PAYMENT_SUCCESSFUL ||
             $statusCode == self::STATUS_REFUND_SUCCESSFUL) &&
             !isset($requestParams['transactionId'])) {
                return false;
        }
                
        if (!$this->_convertAmount($requestParams['transactionAmount'])) {
        	return false;
        }

        if ($requestParams['status'] == self::STATUS_REFUND_SUCCESSFUL ||
            $requestParams['status'] == self::STATUS_REFUND_FAILED) {
	        if (!$this->_convertReferenceId($requestParams['referenceId'])) {
	            return false;
	        }
        }
        
        return true;
    }

    private function _convertRequestParams($requestParams)
    {
        $_tmpResultArray = $this->_convertAmount($requestParams['transactionAmount']);
        unset($requestParams['transactionAmount']);
        $requestParams = array_merge($requestParams, $_tmpResultArray); 

        if ($requestParams['status'] == self::STATUS_REFUND_SUCCESSFUL ||
            $requestParams['status'] == self::STATUS_REFUND_FAILED) {
            $requestParams['referenceId'] = $this->_convertReferenceId($requestParams['referenceId']);
        }

        $requestParams['transactionDate'] = $this->_convertTransactionDate($requestParams['transactionDate']);
        
        return $requestParams;
    }
    
    private function _convertAmount ($requestAmount) 
    {
        $amount = Mage::getSingleton('amazonpayments/api_asp_amount');
        if (!$amount->init($requestAmount)) {
        	return false;
        }
        
    	$resultArray = array(); 
        $resultArray['amount'] = $amount->getValue();
        $resultArray['currencyCode'] = $amount->getCurrencyCode();
        return $resultArray;
    }

    private function _convertReferenceId ($referenceId) 
    {
        $tmpArr = array();
        if (!preg_match("/^Refund\sfor\s([0-9]{9})$/", $referenceId, $tmpArr)) {
            return false;
        }
        return $tmpArr[1];
    }

    private function _convertTransactionDate ($transactionDate) 
    {
    	return Mage::app()->getLocale()->date($transactionDate);
    }    

    private function _setRequestParamsToData($requestParams)
    {
        foreach ($requestParams as $kay => $value) {
            $setMethodName = 'set' . ucfirst($kay); 
            $this->$setMethodName($value);
        }
    }
    
    public function toString($format='')
    {
        $resultString = '';
        foreach($this->getData() as $kay => $value){
        	$resultString .= "[$kay] = $value<br/>"; 
        }
        return $resultString;
    }
}

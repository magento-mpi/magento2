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
class Mage_AmazonPayments_Model_Api_Asp_Fps extends Mage_AmazonPayments_Model_Api_Abstract
{
	const SERVICE_VERSION = '2008-09-17';
	
	const ACTION_CODE_CANCEL = 'Cancel'; 
    const ACTION_CODE_SETTLE = 'Settle'; 
	const ACTION_CODE_REFUND = 'Refund'; 

    const EXCEPTION_INVALID_ACTION_CODE = 10031;	
    const EXCEPTION_INVALID_REQUEST = 10031;    
    const EXCEPTION_INVALID_RESPONSE = 10032;    
    
	public function getRequest($actionCode)
	{
		switch ($actionCode) {
            case self::ACTION_CODE_CANCEL: 
            	$requestModelPath = 'amazonpayments/api_asp_fps_request_cancel';
            	break;  
            case self::ACTION_CODE_SETTLE:
                $requestModelPath = 'amazonpayments/api_asp_fps_request_settle';
                break;  
            case self::ACTION_CODE_REFUND:
                $requestModelPath = 'amazonpayments/api_asp_fps_request_refund';
                break;  
            default: $this->_throwExeptionInvalidActionCode();
		}

		return Mage::getSingleton($requestModelPath)->init($actionCode); 
	}
	
	protected function _getResponse($requestActionCode, $responseBody)
	{
        switch ($requestActionCode) {
            case self::ACTION_CODE_CANCEL: 
                $responseModelPath = 'amazonpayments/api_asp_fps_response_cancel';
                break;  
            case self::ACTION_CODE_SETTLE:
                $responseModelPath = 'amazonpayments/api_asp_fps_response_settle';
                break;  
            case self::ACTION_CODE_REFUND:
                $responseModelPath = 'amazonpayments/api_asp_fps_response_refund';
                break;  
            default: $this->_throwExeptionInvalidActionCode();
        }
    
        $actionResponse = Mage::getSingleton($responseModelPath);
        if ($actionResponse->init($responseBody)) {
            return $actionResponse;
        }
        
        $errorResponse = Mage::getSingleton('amazonpayments/api_asp_fps_response_error');
        if ($errorResponse->init($responseBody)) {
            return $errorResponse;
        }

        throw new Exception(
            Mage::helper('amazonpayments')->__('Response body is not valid FPS respons'), 
            self::EXCEPTION_INVALID_RESPONSE
        );
        	   
	}
	
	public function process($request)
	{
        if (!$request->isValid()) {
	        throw new Exception(
	            Mage::helper('amazonpayments')->__('Invalid request'), 
	            self::EXCEPTION_INVALID_REQUEST
	        );
        }
        
        $request = $this->_addRequiredParameters($request);
        $request = $this->_signRequest($request);
        
        $responseBody = $this->_call($this->_getServiceUrl(), $request->getData());
        return $this->_getResponse($request->getActionCode(), $responseBody);
	}

	protected function _getServiceUrl()
	{
		if ($this->_isSandbox()) {
			return $this->_getConfig('fps_service_url_sandbox');
		}
		return $this->_getConfig('fps_service_url');
	}
	
    protected function _addRequiredParameters($request)
    {
        return $request->setData('AWSAccessKeyId', $this->_getConfig('access_key'))
                    ->setData('Timestamp', gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()))
                    ->setData('Version', self::SERVICE_VERSION)
                    ->setData('SignatureVersion', '1');
    } 

    protected function _signRequest($request)
	{
        $signature = $this->_getSignatureForArray($request->getData(), $this->_getConfig('secret_key'));
        return $request->setData('Signature', $signature);
	}
    

	protected function _call($serviceUrl, $params)
    {
    	$tmpArray = array(); 
        foreach ($params as $kay => $value) {
            $tmpArray[] = $kay . '=' . urlencode($value);		        	  
        }
        $requestBody = implode('&', $tmpArray);
        
        $http = new Varien_Http_Adapter_Curl();
        $http->setConfig(array('timeout' => 30));
        $http->write(Zend_Http_Client::POST, $serviceUrl, '1.1', array(), $requestBody);

        $responseBody = $http->read();
        $responseBody = preg_split('/^\r?$/m', $responseBody, 2);
        $responseBody = trim($responseBody[1]);
        
        $responseBody = new Varien_Simplexml_Element($responseBody);

        $http->close();
        return $responseBody;
    }

    protected function _throwExeptionInvalidActionCode()
    {
        throw new Exception(
            Mage::helper('amazonpayments')->__('Violation of the sequence of states in order'), 
            self::EXCEPTION_INVALID_ACTION_CODE
        );
    }
    
}

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
 * AmazonPayments API wrappers model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Asp extends Mage_AmazonPayments_Model_Api_Abstract
{
    const ERROR_SIGN_REQUEST = 'RS'; 
    const STATUS_REFUND_FAILED = 'RF'; 
    const STATUS_SYSTEM_ERROR = 'SE'; 

    
	public function getPayNowRedirectUrl ($orderId, $amount, $currencyCode) 
	{
		$requestParams = array();
        $requestParams['referenceId'] = $orderId;
		$requestParams['amount'] = $currencyCode . ' ' . $amount; 
		$requestParams['description'] = $this->getPaymentDescription();;
        $requestParams['accessKey'] = $this->getAccessKey();
        $requestParams['processImmediate'] = $this->getProcessImmediate();
		$requestParams['immediateReturn'] = $this->getImmediateReturn();
		$requestParams['collectShippingAddress'] = $this->getCollectShippingAddress();
	   
		$requestParams = $this->signParams($requestParams);
        
		return $this->getPayServiceUrl() . '?' . http_build_query($requestParams);
	}

	public function getIpnRequest ($requestParams) 
	{
		if (!$this->checkSignParams($requestParams)) {
			return false;
		}
		$ipnRequest = Mage::getSingleton('amazonpayments/api_asp_ipn_request');
        if(!$ipnRequest->init($requestParams)) {
        	return false; 	
        }
        return $ipnRequest;
    }
	
}
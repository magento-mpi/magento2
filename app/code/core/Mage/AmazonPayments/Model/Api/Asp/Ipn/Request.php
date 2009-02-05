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
        $this->setData($requestParams);
        $this->addData($this->_convertAmount($requestParams['transactionAmount']));
        return $this;
    }
    
    private function _validateRequestParams($requestParams)
    {
    	if (!isset($requestParams['transactionId']) ||
            !isset($requestParams['referenceId']) ||
            !isset($requestParams['transactionAmount']) ||
            !isset($requestParams['status'])) {
            	return false;
        }
        if (!$this->_convertAmount($requestParams['transactionAmount'])) {
        	return false;
        }        
        return true;
    }

    private function _convertAmount ($requestAmount) 
    {
    	$tmpArr = array();
    	if (!preg_match("/^([A-Z]{3})\s([0-9]{1,}|[0-9]{1,}[.][0-9]{1,})$/", $requestAmount, $tmpArr)) {
    		return false;
    	}
    	$resultArray = array(); 
        $resultArray['amount'] = $tmpArr[2];
        $resultArray['currencyCode'] = $tmpArr[1];
        return $resultArray;
    }
}

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


class Mage_DirectPayment_Model_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
    protected $_code  = 'directpayment';
    protected $_formBlockType = 'directpayment/form';
    protected $_infoBlockType = 'directpayment/info';
    
    /**
     * Availability options
     */
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    
    // no need to debug
    protected $_debugReplacePrivateDataKeys = array();
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Payment/Model/Method/Mage_Payment_Model_Method_Cc#validate()
     */
    public function validate()
    {
        return true;
    }
    
	/**
     * Send authorize request to gateway
     *
     * @param  Varien_Object $payment
     * @param  decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        
    }
    
    /**
     * Get CGI url
     *
     * @return string
     */
    public function getCgiUrl()
    {
        $uri = $this->getConfigData('cgi_url');
        return $uri ? $uri : self::CGI_URL;
    }
    
    /**
     * Return request model for form data building
     *
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function getRequestModel()
    {
        return Mage::getModel('directpayment/authorizenet_request');
    }
    
	/**
     *  Return Order Place Redirect URL.
     *  Need to prevent emails sending for new orders to store's directors.
     *
     *  @return	  string 1
     */
    public function getOrderPlaceRedirectUrl()
    {
        return 1;
    }
    
    /**
     * Generate request object and fill its fields from Quote object
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_DirectPayment_Model_Authorizenet_Request
     */
    public function generateRequestFromOrder(Mage_Sales_Model_Order $order)
    {
        $request = $this->getRequestModel();
        $request->setConstantData($this)
            ->setDataFromOrder($order)
            ->signRequestData();
        return $request;
    }
}